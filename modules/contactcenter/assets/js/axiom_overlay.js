/**
 * AXIOM Intelligent Overlay - JavaScript Controller
 * Non-destructive overlay for CRM dashboard with AI capabilities
 */

(function ($) {
  "use strict";

  // Global state
  window.axiomState = window.axiomState || {
    drawerOpen: false,
    currentLeadId: null,
    currentPhonenumber: null,
    isAnalyzing: false,
    lastMessageId: null,
  };

  let axiomState = window.axiomState;

  // Timing debug: set window.axiomDebugEnabled = true or add ?axiom_debug=1 to URL
  window.axiomTiming = window.axiomTiming || { logs: [], lastReset: 0 };
  function axiomLogTiming(name, ms, extra) {
    const entry = { name, ms: ms != null ? Math.round(ms) : null, extra, t: Date.now() };
    window.axiomTiming.logs.push(entry);
    if (window.axiomDebugEnabled || /axiom_debug=1/.test(location.search)) {
      const msg = ms != null ? name + ": " + ms + "ms" + (extra ? " (" + extra + ")" : "") : name;
      console.log("[AXIOM Timing] " + msg);
      var container = document.getElementById("axiomDebugContainer");
      var panel = document.getElementById("axiomDebugPanel");
      if (container) container.style.display = "block";
      if (panel) {
        var row = document.createElement("div");
        row.className = "axiom-debug-row";
        row.textContent = msg;
        panel.appendChild(row);
        panel.scrollTop = panel.scrollHeight;
      }
    }
  }

  /**
   * Toggle AXIOM Drawer - Exposed globally
   */
  window.toggleAXIOMDrawer = function () {
    const drawer = document.getElementById("axiomDrawer");
    const trigger = document.querySelector(".axiom-trigger-btn");
    const body = document.body;

    if (!drawer) return;

    axiomState.drawerOpen = !axiomState.drawerOpen;

    if (axiomState.drawerOpen) {
      drawer.classList.add("active");
      if (trigger) trigger.classList.add("active");

      // Close left sidebar using hide-menu button - this ensures proper state management
      // Only close if sidebar is currently visible
      const bodyEl = typeof $ !== "undefined" ? $("body") : null;
      if (bodyEl && !bodyEl.hasClass("hide-sidebar")) {
        closeLeftSidebar();
      }

      // Add body class for layout adjustments
      body.classList.add("axiom-drawer-active");

      // Reset skeleton state so shimmers show correctly while loading
      resetAXIOMSkeleton();

      loadAXIOMData();
    } else {
      drawer.classList.remove("active");
      if (trigger) trigger.classList.remove("active");

      // Remove body class
      body.classList.remove("axiom-drawer-active");
    }
  };

  /**
   * Close left sidebar menu - Trigger the same function as hide-menu button
   */
  function closeLeftSidebar() {
    if (typeof $ === "undefined") {
      return;
    }

    try {
      const body = $("body");
      const $hideMenuBtn = $(".hide-menu.tw-ml-1, .hide-menu");

      // Check if sidebar is currently visible (body doesn't have hide-sidebar class)
      const isSidebarVisible = !body.hasClass("hide-sidebar");

      if (isSidebarVisible && $hideMenuBtn.length > 0) {
        // Sidebar is visible, need to close it using hide-menu button
        // This will toggle hide-sidebar class on body
        $hideMenuBtn.trigger("click");

        // Double-check after a small delay and force if needed
        setTimeout(function () {
          if (!body.hasClass("hide-sidebar")) {
            // Force close if click didn't work
            body.removeClass("show-sidebar").addClass("hide-sidebar");
          }
        }, 100);
      } else if (isSidebarVisible && $hideMenuBtn.length === 0) {
        // Button not found, manually toggle the class
        body.removeClass("show-sidebar").addClass("hide-sidebar");
      }
    } catch (e) {
      console.error("AXIOM: Error closing sidebar", e);
      // Last resort fallback
      try {
        const body = $("body");
        if (!body.hasClass("hide-sidebar")) {
          body.removeClass("show-sidebar").addClass("hide-sidebar");
        }
      } catch (e2) {
        console.error("AXIOM: Fallback failed", e2);
      }
    }
  }

  /**
   * Format DeepQuery answer with markdown-like styling
   */
  function formatDeepQueryAnswer(text) {
    if (!text) return "";

    // Convert markdown-style formatting to HTML
    let formatted = String(text);

    // Bold **text**
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

    // Italic *text* (but not if it's part of **text**)
    formatted = formatted.replace(
      /(?<!\*)\*(?!\*)([^*]+?)(?<!\*)\*(?!\*)/g,
      "<em>$1</em>",
    );

    // Numbered lists (1. item or 1) item)
    const lines = formatted.split("\n");
    const formattedLines = [];
    let inList = false;

    for (let i = 0; i < lines.length; i++) {
      const line = lines[i];
      const numberedMatch = line.match(/^\s*(\d+)\.\s+(.+)$/);
      const bulletMatch = line.match(/^\s*[-•]\s+(.+)$/);

      if (numberedMatch || bulletMatch) {
        if (!inList) {
          formattedLines.push("<ol>");
          inList = true;
        }
        const content = numberedMatch ? numberedMatch[2] : bulletMatch[1];
        formattedLines.push("<li>" + content + "</li>");
      } else {
        if (inList) {
          formattedLines.push("</ol>");
          inList = false;
        }
        if (line.trim()) {
          formattedLines.push("<p>" + line + "</p>");
        } else {
          formattedLines.push("");
        }
      }
    }
    if (inList) {
      formattedLines.push("</ol>");
    }

    formatted = formattedLines.join("\n");

    // Replace remaining line breaks with <br>
    formatted = formatted.replace(/\n/g, "<br>");

    // Clean up empty paragraphs
    formatted = formatted.replace(/<p>\s*<\/p>/g, "");
    formatted = formatted.replace(/<p><br><\/p>/g, "");

    return formatted;
  }

  /**
   * Typewriter Effect - Character by character rendering for plain text
   */
  function typewriterEffect(element, text, speed = 20) {
    return new Promise((resolve) => {
      element.textContent = "";
      element.style.borderRight = "2px solid #00e09b";

      let i = 0;
      const typeInterval = setInterval(() => {
        if (i < text.length) {
          element.textContent += text.charAt(i);
          i++;
        } else {
          clearInterval(typeInterval);
          setTimeout(() => {
            element.style.borderRight = "none";
            resolve();
          }, 300);
        }
      }, speed);
    });
  }

  /**
   * Typewriter Effect for formatted HTML
   */
  function typewriterEffectFormatted(element, html, speed = 15) {
    return new Promise((resolve) => {
      element.innerHTML = "";
      element.style.borderRight = "2px solid #00e09b";

      // Extract text content for typewriter effect
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;
      const textContent = tempDiv.textContent || tempDiv.innerText || "";

      let currentIndex = 0;
      let displayedHTML = "";

      // Function to get HTML up to a certain character position
      function getHTMLUpToChar(htmlText, targetCharIndex) {
        if (targetCharIndex >= textContent.length) {
          return html;
        }

        // Simple approach: gradually reveal HTML
        // For better UX, we'll use a simpler method
        const progress = Math.min(targetCharIndex / textContent.length, 1);
        const htmlLength = html.length;
        const visibleLength = Math.floor(htmlLength * progress);

        // Find safe break points (end of tags)
        let safeBreak = visibleLength;
        while (safeBreak < htmlLength && html.charAt(safeBreak) !== ">") {
          safeBreak++;
        }
        if (safeBreak < htmlLength) safeBreak++;

        return html.substring(0, safeBreak);
      }

      const typeInterval = setInterval(() => {
        if (currentIndex < textContent.length) {
          displayedHTML = getHTMLUpToChar(html, currentIndex);
          element.innerHTML = displayedHTML;
          currentIndex += 2; // Speed up a bit for HTML
        } else {
          clearInterval(typeInterval);
          // Set final HTML with all formatting
          element.innerHTML = html;
          setTimeout(() => {
            element.style.borderRight = "none";
            resolve();
          }, 300);
        }
      }, speed);
    });
  }

  /**
   * Reset skeleton state - show all shimmers, hide all content (call when drawer opens)
   */
  function resetAXIOMSkeleton() {
    const shimmerIds = [
      "dealPulseShimmer",
      "clientDNAShimmer",
      "stratPathShimmer",
    ];
    shimmerIds.forEach(function (id) {
      const el = document.getElementById(id);
      if (el) {
        el.style.display = "block";
        el.style.visibility = "visible";
      }
    });
    $("#dealPulseContent").hide();
    $("#clientDNAAnalysis").hide();
    $("#stratPathCards").hide();
  }

  /**
   * Shimmer Loading State
   */
  function showShimmer(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
      element.style.display = "block";
      element.style.visibility = "visible";
    }
  }

  function hideShimmer(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
      element.style.display = "none";
    }
  }

  // Helper to show/hide elements
  function showElement(elementId) {
    $("#" + elementId).show();
  }

  function hideElement(elementId) {
    $("#" + elementId).hide();
  }

  /**
   * Load AXIOM Data - Quick Reply + SmartChips only. DealPulse, ClientDNA, StratPath load on expand (lazy).
   */
  window.axiomSectionLoaded = window.axiomSectionLoaded || { deal_pulse: false, client_dna: false, strat_path: false };

  function loadAXIOMData() {
    const phonenumber = $("input[name='phonenumber']").val();
    const leadId = phonenumber;

    if (!phonenumber) {
      return;
    }

    axiomState.currentLeadId = leadId;
    axiomState.currentPhonenumber = phonenumber;

    if (window.axiomDebugEnabled || /axiom_debug=1/.test(location.search)) {
      var p = document.getElementById("axiomDebugPanel");
      if (p) p.innerHTML = "";
    }

    loadSmartChips(leadId);
    axiomLogTiming("Quick Reply: auto-start (parallel)", null);
    generateQuickReplies();

    var $dp = $("#dealPulseSection"), $cd = $("#clientDNASection"), $sp = $("#stratPathSection");
    if ($dp.length && !$dp.hasClass("axiom-collapsed")) loadDealPulse(leadId);
    if ($cd.length && !$cd.hasClass("axiom-collapsed")) loadClientDNA(leadId);
    if ($sp.length && !$sp.hasClass("axiom-collapsed")) loadStratPath(leadId);
  }

  window.toggleAxiomSection = function (sectionId) {
    var $section = $("#" + sectionId);
    var feature = $section.data("feature");
    var leadId = axiomState.currentLeadId;

    if ($section.hasClass("axiom-collapsed")) {
      $section.removeClass("axiom-collapsed");
      $section.find(".axiom-chevron").removeClass("fa-chevron-right").addClass("fa-chevron-down");

      if (!window.axiomSectionLoaded[feature] && leadId) {
        window.axiomSectionLoaded[feature] = true;
        if (feature === "deal_pulse") loadDealPulse(leadId);
        else if (feature === "client_dna") loadClientDNA(leadId);
        else if (feature === "strat_path") loadStratPath(leadId);
      }
    } else {
      $section.addClass("axiom-collapsed");
      $section.find(".axiom-chevron").removeClass("fa-chevron-down").addClass("fa-chevron-right");
    }
  };

  /**
   * Refresh AXIOM Intelligence - Update all AI features (uses bundle for one request)
   */
  window.refreshAXIOMIntelligence = function () {
    if (!axiomState.drawerOpen || !axiomState.currentLeadId) {
      console.log("AXIOM: Skipping refresh - drawer not open or no lead ID");
      return;
    }
    console.log(
      "🔄 AXIOM: Refreshing intelligence for lead:",
      axiomState.currentLeadId,
    );
    clearTimeout(window.axiomUpdateTimeout);
    clearTimeout(window.axiomPeriodicUpdateTimeout);
    clearTimeout(window.axiomRefreshTimeout);
    window.axiomRefreshTimeout = setTimeout(function () {
      loadAXIOMData();
    }, 300);
  };

  function applyDealPulseUI(response) {
    hideShimmer("dealPulseShimmer");
    if (!response) {
      $("#dealPulseBar")
        .css("width", "50%")
        .css("background-color", getScoreColor(50));
      $("#dealPulseScore")
        .text("50%")
        .removeClass("dealpulse-cold dealpulse-warm dealpulse-hot")
        .addClass("dealpulse-warm");
      $("#dealPulseLabel").text("Morno");
      $("#dealPulseContent").fadeIn();
      return;
    }
    let score =
      response.success && response.score !== undefined
        ? parseInt(response.score) || 0
        : response.score !== undefined
          ? parseInt(response.score) || 0
          : 50;
    let label = response.label || "Morno";
    const labelMap = {
      Cold: "Frio",
      Warm: "Morno",
      Hot: "Quente",
      Frio: "Frio",
      Morno: "Morno",
      Quente: "Quente",
    };
    const translatedLabel = labelMap[label] || label;
    const colorClass = getScoreColorClass(score);
    $("#dealPulseBar")
      .css("width", score + "%")
      .css("background-color", getScoreColor(score));
    $("#dealPulseScore")
      .text(score + "%")
      .removeClass("dealpulse-cold dealpulse-warm dealpulse-hot")
      .addClass(colorClass);
    $("#dealPulseLabel").text(translatedLabel);
    $("#dealPulseContent").fadeIn();
  }

  /**
   * DealPulse - Predictive Scoring
   */
  function loadDealPulse(leadId) {
    if (!leadId) {
      console.warn("DealPulse: No lead ID provided");
      return;
    }
    showShimmer("dealPulseShimmer");
    $("#dealPulseContent").hide();
    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_deal_pulse",
      method: "POST",
      data: { lead_id: leadId },
      dataType: "json",
      success: function (response) {
        applyDealPulseUI(response);
      },
      error: function (xhr, status, error) {
        hideShimmer("dealPulseShimmer");
        console.error("DealPulse: AJAX error", {
          status: status,
          error: error,
          xhr: xhr,
        });
        $("#dealPulseLabel").text("Erro ao carregar");
        $("#dealPulseContent").fadeIn();
      },
    });
  }

  function getScoreColorClass(score) {
    if (score <= 30) return "dealpulse-cold";
    if (score <= 70) return "dealpulse-warm";
    return "dealpulse-hot";
  }

  function getScoreColor(score) {
    if (score <= 30) return "#e63946";
    if (score <= 70) return "#ffb703";
    return "#00e09b";
  }

  function applyClientDNAUI(response) {
    hideShimmer("clientDNAShimmer");
    if (!response || !response.success) {
      $("#clientDNAAnalysis").fadeIn();
      return;
    }
    const sentiment = response.sentiment || {};
    const emoji = sentiment.emoji || "🤔";
    let label = sentiment.label || "Neutro";
    const sentimentMap = {
      Neutral: "Neutro",
      Positive: "Positivo",
      Negative: "Negativo",
      Curious: "Curioso",
      Frustrated: "Frustrado",
      Interested: "Interessado",
      Desconfiado: "Desconfiado",
    };
    label = sentimentMap[label] || label;
    const tags = response.tags || [];
    $("#clientDNAEmoji").text(emoji);
    $("#clientDNALabel").text(label);
    $("#clientDNATags").html(
      tags.map((tag) => `<span class="clientdna-tag">#${tag}</span>`).join(""),
    );
    $("#clientDNAAnalysis").fadeIn();
  }

  /**
   * ClientDNA - Sentiment Analysis
   */
  window.clientDNARequestId = window.clientDNARequestId || 0;
  window.clientDNALastRequestTime = window.clientDNALastRequestTime || 0;

  function loadClientDNA(leadId) {
    const now = Date.now();
    if (now - window.clientDNALastRequestTime < 1000) return;
    window.clientDNALastRequestTime = now;
    const requestId = ++window.clientDNARequestId;
    showShimmer("clientDNAShimmer");
    $("#clientDNAAnalysis").hide();
    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_client_dna",
      method: "POST",
      data: { lead_id: leadId },
      dataType: "json",
      success: function (response) {
        if (requestId < window.clientDNARequestId) return;
        applyClientDNAUI(response);
      },
      error: function (xhr, status, error) {
        if (requestId >= window.clientDNARequestId) {
          hideShimmer("clientDNAShimmer");
          console.error("AXIOM ClientDNA: AJAX error", {
            status: status,
            error: error,
          });
        }
      },
    });
  }

  /**
   * EchoSense - Audio Transcription (Triggered when audio detected)
   */
  window.detectAudioMessage = function (messageId, audioUrl) {
    $("#echoSenseBlock").fadeIn();
    showShimmer("echoSenseShimmer");

    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_echo_sense",
      method: "POST",
      data: {
        message_id: messageId,
        audio_url: audioUrl,
        lead_id: axiomState.currentLeadId,
      },
      dataType: "json",
      success: function (response) {
        hideShimmer("echoSenseShimmer");

        if (response.success) {
          const transcript = response.transcript || "";
          const tone = response.tone || "";

          let transcriptHtml = "";
          if (transcript) {
            transcriptHtml += `<p><strong>Transcript:</strong> ${transcript}</p>`;
          }
          if (tone) {
            transcriptHtml += `<p style="margin-top: 10px; font-style: italic; color: rgba(255,255,255,0.7);"><i class="fa-solid fa-info-circle"></i> ${tone}</p>`;
          }

          $("#echoSenseTranscript").html(transcriptHtml);
        }
      },
      error: function () {
        hideShimmer("echoSenseShimmer");
      },
    });
  };

  window.toggleTranscript = function () {
    const transcript = $("#echoSenseTranscript");
    const btn = $(".echosense-toggle-transcript");

    if (transcript.hasClass("active")) {
      transcript.removeClass("active").slideUp();
      btn.html('<i class="fa-solid fa-text"></i> Show Transcript');
    } else {
      transcript.addClass("active").slideDown();
      btn.html('<i class="fa-solid fa-eye-slash"></i> Hide Transcript');
    }
  };

  function applyStratPathUI(response) {
    hideShimmer("stratPathShimmer");
    if (
      !response ||
      !response.success ||
      !response.strategies ||
      response.strategies.length === 0
    ) {
      $("#stratPathCards")
        .html(
          '<p style="color: rgba(255,255,255,0.5); padding: 20px; text-align: center;">Nenhuma estratégia disponível no momento</p>',
        )
        .fadeIn();
      return;
    }
    const cardsHtml = response.strategies
      .map(
        (strategy, index) => `
            <div class="stratpath-card">
                <h5 class="stratpath-card-title">${strategy.title || "Estratégia " + (index + 1)}</h5>
                <p class="stratpath-card-preview">${strategy.preview || ""}</p>
                <button class="stratpath-use-btn" onclick="useStratPath('${index}')">
                    <i class="fa-solid fa-check"></i> Usar Esta
                </button>
            </div>
        `,
      )
      .join("");
    $("#stratPathCards").html(cardsHtml).fadeIn();
    window.axiomStrategies = response.strategies;
  }

  /**
   * StratPath Engine - Strategic Suggestions
   */
  function loadStratPath(leadId) {
    showShimmer("stratPathShimmer");
    $("#stratPathCards").hide();
    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_strat_path",
      method: "POST",
      data: { lead_id: leadId },
      dataType: "json",
      success: function (response) {
        applyStratPathUI(response);
      },
      error: function (xhr, status, error) {
        hideShimmer("stratPathShimmer");
        console.error("StratPath: AJAX error", {
          status: status,
          error: error,
          xhr: xhr,
        });
        $("#stratPathCards")
          .html(
            '<p style="color: rgba(255,255,255,0.5); padding: 20px; text-align: center;">Erro ao carregar estratégias</p>',
          )
          .fadeIn();
      },
    });
  }

  window.useStratPath = function (index) {
    if (!window.axiomStrategies || !window.axiomStrategies[index]) return;

    const strategy = window.axiomStrategies[index];
    const message = strategy.message || strategy.preview || "";

    if (message) {
      typeToInput(message);
    }
  };

  /**
   * FlowSync - Auto Actions (Calendar/CRM tasks)
   */
  window.detectFlowSyncIntent = function (messageText) {
    // Check for date/time patterns
    const datePatterns = [
      /(?:let's meet|let's schedule|agendar|marcar).*?(\w+day|\d{1,2}[\/\-]\d{1,2}|\d{1,2}\s+(?:am|pm|AM|PM))/i,
      /(?:tuesday|wednesday|thursday|friday|saturday|sunday|terça|quarta|quinta|sexta|sábado|domingo).*?(\d{1,2}[:h]\d{2}|(?:\d{1,2})\s*(?:am|pm|AM|PM))/i,
    ];

    let match = null;
    for (let pattern of datePatterns) {
      match = messageText.match(pattern);
      if (match) break;
    }

    if (match) {
      showFlowSyncCard({
        title: "📅 Schedule Meeting",
        description: `Detected: ${match[0]}`,
        actions: [
          { label: "Add to Calendar", action: "calendar" },
          { label: "Update CRM Stage", action: "crm_stage" },
        ],
      });
    }
  };

  function showFlowSyncCard(data) {
    const container = $("#flowSyncContainer");
    const cardHtml = `
            <div class="flowsync-action-card">
                <h5 class="flowsync-action-title">
                    <i class="fa-solid fa-sparkles"></i>
                    ${data.title}
                </h5>
                <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin: 0 0 10px 0;">${data.description}</p>
                <div class="flowsync-action-buttons">
                    ${data.actions
                      .map(
                        (action) =>
                          `<button class="flowsync-action-btn" onclick="executeFlowSync('${action.action}')">
                            ${action.label}
                        </button>`,
                      )
                      .join("")}
                </div>
            </div>
        `;

    container.html(cardHtml).fadeIn();
  }

  window.executeFlowSync = function (action) {
    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_flow_sync",
      method: "POST",
      data: {
        action: action,
        lead_id: axiomState.currentLeadId,
        phonenumber: axiomState.currentPhonenumber,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          alert_float(
            "success",
            response.message || "Action executed successfully",
          );
          $("#flowSyncContainer").fadeOut();
        } else {
          alert_float("danger", response.message || "Action failed");
        }
      },
      error: function () {
        alert_float("danger", "Error executing action");
      },
    });
  };

  /**
   * DeepQuery - RAG Chat Interface
   */
  window.sendDeepQuery = function () {
    const input = $("#deepQueryInput");
    const query = input.val().trim();

    if (!query) return;

    const responseDiv = $("#deepQueryResponse");
    responseDiv
      .html('<div class="axiom-shimmer" style="height: 60px;"></div>')
      .fadeIn();

    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_deep_query",
      method: "POST",
      data: {
        query: query,
        lead_id: axiomState.currentLeadId,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const answer = response.answer || "No answer available";
          responseDiv.empty();

          // Format answer with markdown-like styling
          const formattedAnswer = formatDeepQueryAnswer(answer);

          // Use typewriter effect with formatted HTML
          typewriterEffectFormatted(responseDiv[0], formattedAnswer, 15).then(
            () => {
              input.val("");
            },
          );
        } else {
          responseDiv.html(
            '<p style="color: rgba(255,255,255,0.7);">' +
              (response.message || "Error getting answer") +
              "</p>",
          );
        }
      },
      error: function () {
        responseDiv.html(
          '<p style="color: rgba(255,255,255,0.7);">Error connecting to AXIOM</p>',
        );
      },
    });
  };

  // Enter key for DeepQuery
  $(document).on("keypress", "#deepQueryInput", function (e) {
    if (e.which === 13) {
      sendDeepQuery();
    }
  });

  /**
   * Quick Reply - Objectives & Products registry (localStorage)
   * Both can be selected independently and combined for API
   */
  const AXIOM_OBJECTIVES_KEY = "axiom_quickreply_objectives";
  const AXIOM_PRODUCTS_KEY = "axiom_quickreply_products";
  window.axiomObjectives = window.axiomObjectives || [];
  window.axiomProducts = window.axiomProducts || [];
  window.axiomSelectedObjectiveIndex = window.axiomSelectedObjectiveIndex ?? -1;
  window.axiomSelectedProductIndex = window.axiomSelectedProductIndex ?? -1;

  function loadAxiomObjectives() {
    try {
      var raw = localStorage.getItem(AXIOM_OBJECTIVES_KEY);
      window.axiomObjectives = raw ? JSON.parse(raw) : [];
    } catch (e) {
      window.axiomObjectives = [];
    }
  }
  function saveAxiomObjectives() {
    try {
      localStorage.setItem(AXIOM_OBJECTIVES_KEY, JSON.stringify(window.axiomObjectives));
    } catch (e) {}
  }
  function loadAxiomProducts() {
    try {
      var raw = localStorage.getItem(AXIOM_PRODUCTS_KEY);
      window.axiomProducts = raw ? JSON.parse(raw) : [];
    } catch (e) {
      window.axiomProducts = [];
    }
  }
  function saveAxiomProducts() {
    try {
      localStorage.setItem(AXIOM_PRODUCTS_KEY, JSON.stringify(window.axiomProducts));
    } catch (e) {}
  }

  function renderQuickReplyObjectives() {
    var list = document.getElementById("quickReplyObjectivesList");
    if (!list) return;
    list.innerHTML = "";
    window.axiomObjectives.forEach(function (obj, idx) {
      var chip = document.createElement("span");
      chip.className = "quickreply-objective-chip" + (window.axiomSelectedObjectiveIndex === idx ? " selected" : "");
      chip.setAttribute("data-idx", idx);
      chip.innerHTML = '<span class="chip-text">' + (obj.replace(/</g, "&lt;").replace(/>/g, "&gt;")) + '</span><span class="chip-delete" data-idx="' + idx + '"><i class="fa-solid fa-times"></i></span>';
      list.appendChild(chip);
    });
  }
  function renderQuickReplyProducts() {
    var list = document.getElementById("quickReplyProductsList");
    if (!list) return;
    list.innerHTML = "";
    window.axiomProducts.forEach(function (obj, idx) {
      var chip = document.createElement("span");
      chip.className = "quickreply-product-chip" + (window.axiomSelectedProductIndex === idx ? " selected" : "");
      chip.setAttribute("data-idx", idx);
      chip.innerHTML = '<span class="chip-text">' + (obj.replace(/</g, "&lt;").replace(/>/g, "&gt;")) + '</span><span class="chip-delete" data-idx="' + idx + '"><i class="fa-solid fa-times"></i></span>';
      list.appendChild(chip);
    });
  }

  window.addQuickReplyObjective = function (text) {
    var t = (text || "").trim();
    if (!t) return;
    if (window.axiomObjectives.indexOf(t) >= 0) return;
    window.axiomObjectives.push(t);
    saveAxiomObjectives();
    renderQuickReplyObjectives();
  };
  window.removeQuickReplyObjective = function (idx) {
    window.axiomObjectives.splice(idx, 1);
    if (window.axiomSelectedObjectiveIndex === idx) window.axiomSelectedObjectiveIndex = -1;
    else if (window.axiomSelectedObjectiveIndex > idx) window.axiomSelectedObjectiveIndex--;
    saveAxiomObjectives();
    renderQuickReplyObjectives();
  };
  window.selectQuickReplyObjective = function (idx) {
    window.axiomSelectedObjectiveIndex = window.axiomSelectedObjectiveIndex === idx ? -1 : idx;
    renderQuickReplyObjectives();
    scheduleQuickReplyRegenerate();
  };

  window.addQuickReplyProduct = function (text) {
    var t = (text || "").trim();
    if (!t) return;
    if (window.axiomProducts.indexOf(t) >= 0) return;
    window.axiomProducts.push(t);
    saveAxiomProducts();
    renderQuickReplyProducts();
  };
  window.removeQuickReplyProduct = function (idx) {
    window.axiomProducts.splice(idx, 1);
    if (window.axiomSelectedProductIndex === idx) window.axiomSelectedProductIndex = -1;
    else if (window.axiomSelectedProductIndex > idx) window.axiomSelectedProductIndex--;
    saveAxiomProducts();
    renderQuickReplyProducts();
  };
  window.selectQuickReplyProduct = function (idx) {
    window.axiomSelectedProductIndex = window.axiomSelectedProductIndex === idx ? -1 : idx;
    renderQuickReplyProducts();
    scheduleQuickReplyRegenerate();
  };

  window.axiomQuickReplyRegenerateTimeout = null;
  function scheduleQuickReplyRegenerate() {
    clearTimeout(window.axiomQuickReplyRegenerateTimeout);
    window.axiomQuickReplyRegenerateTimeout = setTimeout(function () {
      if (axiomState.drawerOpen && axiomState.currentLeadId) {
        generateQuickReplies();
      }
    }, 400);
  }

  function getSelectedQuickReplyObjective() {
    var parts = [];
    if (window.axiomSelectedObjectiveIndex >= 0 && window.axiomObjectives[window.axiomSelectedObjectiveIndex]) {
      parts.push("Objective: " + window.axiomObjectives[window.axiomSelectedObjectiveIndex]);
    }
    if (window.axiomSelectedProductIndex >= 0 && window.axiomProducts[window.axiomSelectedProductIndex]) {
      parts.push("Product: " + window.axiomProducts[window.axiomSelectedProductIndex]);
    }
    return parts.join(". ");
  }

  /**
   * Quick Reply - STREAMING: tokens appear as generated for faster perceived response
   */
  window.generateQuickReplies = function () {
    const leadId = axiomState.currentLeadId;
    if (!leadId) {
      alert_float("warning", "Select a conversation first");
      return;
    }
    const context = $("#quickReplyContext").val() || "last_message";
    const objective = getSelectedQuickReplyObjective();
    const btn = $("#quickReplyGenerateBtn");
    const thinking = $("#quickReplyThinking");
    const thinkingText = $("#quickReplyThinkingText");
    const suggestions = $("#quickReplySuggestions");
    const block = $("#quickReplyBlock");

    btn.prop("disabled", true);
    suggestions.show().empty();
    window.axiomQuickReplies = [];
    const steps = [
      block.data("thinking-1") || "Analyzing conversation...",
      block.data("thinking-2") || "Understanding context...",
      block.data("thinking-3") || "Generating best options...",
    ];
    let stepIdx = 0;
    thinkingText.text(steps[0]);
    thinking.show();

    const stepInterval = setInterval(function () {
      stepIdx = (stepIdx + 1) % steps.length;
      thinkingText.text(steps[stepIdx]);
    }, 1400);

    const done = function (errMsg) {
      clearInterval(stepInterval);
      thinking.hide();
      btn.prop("disabled", false);
      if (errMsg) {
        suggestions.html(
          '<p style="color: rgba(255,255,255,0.6); font-size: 13px;">' +
            errMsg +
            "</p>",
        );
      }
      if (window.axiomQuickReplies.length === 0 && !errMsg) {
        suggestions.html(
          '<p style="color: rgba(255,255,255,0.6); font-size: 13px;">No suggestions. Try another context or objective.</p>',
        );
      }
    };

    var lastResponseLength = 0;
    var buffer = "";
    var fullText = "";
    var tQuickStart = performance.now();
    var firstChunkLogged = false;

    axiomLogTiming("Quick Reply: request started", null);

    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_quick_replies_stream",
      method: "POST",
      data: {
        lead_id: leadId,
        context: context,
        objective: objective,
      },
      xhrFields: {
        onprogress: function (e) {
          var response = e.currentTarget.response;
          var newChunk = response.substring(lastResponseLength);
          lastResponseLength = response.length;

          buffer += newChunk;
          var lines = buffer.split("\n");
          buffer = lines.pop() || "";
          for (var i = 0; i < lines.length; i++) {
            var line = lines[i].trim();
            if (line.indexOf("data: ") === 0) {
              var jsonStr = line.slice(6);
              if (!jsonStr) continue;
              try {
                var data = JSON.parse(jsonStr);
                if (data.error) {
                  done(data.error);
                  return;
                }
                if (data.done) {
                  var finalParts = fullText
                    .split(/\r?\n/)
                    .map(function (s) {
                      return s.trim();
                    })
                    .filter(function (s) {
                      return s.length > 3;
                    });
                  for (
                    var k = window.axiomQuickReplies.length;
                    k < finalParts.length;
                    k++
                  ) {
                    var txt = finalParts[k];
                    window.axiomQuickReplies.push(txt);
                    if (window.axiomQuickReplies.length === 1) thinking.hide();
                    var idx = window.axiomQuickReplies.length - 1;
                    var safe = txt
                      .replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;")
                      .replace(/\n/g, "<br>");
                    suggestions.append(
                      '<div class="quickreply-suggestion-card" onclick="useQuickReply(' +
                        idx +
                        ')">' +
                        safe +
                        '<span class="use-label"><i class="fa-solid fa-check"></i> Click to use</span></div>',
                    );
                  }
                  axiomLogTiming("Quick Reply: completed", Math.round(performance.now() - tQuickStart), window.axiomQuickReplies.length + " replies");
                  done();
                  return;
                }
                if (data.delta) {
                  if (!firstChunkLogged) {
                    firstChunkLogged = true;
                    axiomLogTiming("Quick Reply: first chunk", Math.round(performance.now() - tQuickStart), "streaming started");
                  }
                  fullText += data.delta;
                  var parts = fullText.split(/\r?\n/);
                  var completeParts = parts
                    .slice(0, -1)
                    .map(function (s) {
                      return s.trim();
                    })
                    .filter(function (s) {
                      return s.length > 3;
                    });
                  var prevCount = window.axiomQuickReplies.length;
                  for (var j = prevCount; j < completeParts.length; j++) {
                    var txt = completeParts[j];
                    window.axiomQuickReplies.push(txt);
                    if (window.axiomQuickReplies.length === 1) thinking.hide();
                    var idx = window.axiomQuickReplies.length - 1;
                    var safe = txt
                      .replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;")
                      .replace(/\n/g, "<br>");
                    suggestions.append(
                      '<div class="quickreply-suggestion-card" onclick="useQuickReply(' +
                        idx +
                        ')">' +
                        safe +
                        '<span class="use-label"><i class="fa-solid fa-check"></i> Click to use</span></div>',
                    );
                  }
                }
              } catch (e) {}
            }
          }
        },
      },
      success: function () {
        axiomLogTiming("Quick Reply: completed", Math.round(performance.now() - tQuickStart));
        done();
      },
      error: function () {
        axiomLogTiming("Quick Reply: failed", Math.round(performance.now() - tQuickStart));
        done("Error loading suggestions");
      },
    });
  };

  window.useQuickReply = function (index) {
    if (!window.axiomQuickReplies || !window.axiomQuickReplies[index]) return;
    const text = window.axiomQuickReplies[index];
    if (text) {
      typeToInput(text);
    }
  };

  /**
   * SmartChips - Quick Suggestions
   */
  function loadSmartChips(leadId) {
    const container = $("#smartchipsContainer");
    var tStart = performance.now();
    axiomLogTiming("SmartChips: request sent", null);

    $.ajax({
      url: site_url + "admin/contactcenter/ajax_axiom_smart_chips",
      method: "POST",
      data: { lead_id: leadId },
      dataType: "json",
      success: function (response) {
        axiomLogTiming("SmartChips: completed", Math.round(performance.now() - tStart), response.chips ? response.chips.length + " chips" : "");
        if (response.success && response.chips && response.chips.length > 0) {
          let chipsHtml = "";
          response.chips.forEach((chip, index) => {
            chipsHtml += `<span class="smartchip" onclick="useSmartChip(${index})">${chip.label}</span>`;
          });

          container.html(chipsHtml).fadeIn();

          // Store chips for later use
          window.axiomChips = response.chips;
        } else {
          container.hide();
        }
      },
      error: function () {
        axiomLogTiming("SmartChips: failed", Math.round(performance.now() - tStart));
        container.hide();
      },
    });
  }

  window.useSmartChip = function (index) {
    if (!window.axiomChips || !window.axiomChips[index]) return;

    const chip = window.axiomChips[index];
    const message = chip.message || chip.label || "";

    if (message) {
      typeToInput(message);
      $("#smartchipsContainer").fadeOut();
    }
  };

  /**
   * Type to Input - Typewriter effect to fill input
   * Shows send button and highlights it so user knows to click to send
   */
  function typeToInput(text) {
    const textarea = $("#textarea-chat");
    const emojioneArea = textarea[0] && textarea[0].emojioneArea;
    const btnSubmit = $("#btn_submit");

    // Clear existing text
    if (emojioneArea) {
      emojioneArea.setText("");
    } else {
      textarea.val("");
    }

    // Focus on input
    textarea.focus();

    // Typewriter effect
    let currentText = "";
    let i = 0;
    const typeInterval = setInterval(() => {
      if (i < text.length) {
        currentText += text.charAt(i);
        if (emojioneArea) {
          emojioneArea.setText(currentText);
        } else {
          textarea.val(currentText);
        }
        i++;
      } else {
        clearInterval(typeInterval);
        // Trigger input so send button appears (it listens to input event)
        textarea.trigger("input");
        // Ensure send button shows and mic hides (in case emojioneArea doesn't fire input)
        btnSubmit.removeClass("hidden").prop("disabled", false).css("background", "var(--primary)");
        $("#startRecording").addClass("hidden");
        // Highlight send button so user notices they need to click it
        btnSubmit.addClass("axiom-send-highlight");
        setTimeout(function() { btnSubmit.removeClass("axiom-send-highlight"); }, 2500);
      }
    }, 20);
  }

  /**
   * Detect mobile device
   */
  function isMobileDevice() {
    return (
      window.innerWidth <= 768 ||
      /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent,
      )
    );
  }

  /**
   * Listen for new messages to update AXIOM features
   */
  $(document).ready(function () {
    // Objectives registry
    loadAxiomObjectives();
    renderQuickReplyObjectives();
    $(document).on("click", ".quickreply-objective-chip", function (e) {
      if ($(e.target).closest(".chip-delete").length) return;
      var idx = parseInt($(this).data("idx"), 10);
      if (!isNaN(idx)) selectQuickReplyObjective(idx);
    });
    $(document).on("click", ".quickreply-objective-chip .chip-delete", function (e) {
      e.stopPropagation();
      var idx = parseInt($(this).data("idx"), 10);
      if (!isNaN(idx)) removeQuickReplyObjective(idx);
    });
    $("#quickReplyAddObjectiveBtn").on("click", function () {
      var val = $("#quickReplyObjectiveNew").val().trim();
      if (val) {
        addQuickReplyObjective(val);
        $("#quickReplyObjectiveNew").val("");
      }
    });
    $("#quickReplyObjectiveNew").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        $("#quickReplyAddObjectiveBtn").click();
      }
    });

    // Products registry
    loadAxiomProducts();
    renderQuickReplyProducts();
    $(document).on("click", ".quickreply-product-chip", function (e) {
      if ($(e.target).closest(".chip-delete").length) return;
      var idx = parseInt($(this).data("idx"), 10);
      if (!isNaN(idx)) selectQuickReplyProduct(idx);
    });
    $(document).on("click", ".quickreply-product-chip .chip-delete", function (e) {
      e.stopPropagation();
      var idx = parseInt($(this).data("idx"), 10);
      if (!isNaN(idx)) removeQuickReplyProduct(idx);
    });
    $("#quickReplyAddProductBtn").on("click", function () {
      var val = $("#quickReplyProductNew").val().trim();
      if (val) {
        addQuickReplyProduct(val);
        $("#quickReplyProductNew").val("");
      }
    });
    $("#quickReplyProductNew").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        $("#quickReplyAddProductBtn").click();
      }
    });

    // Add mobile class to body if needed
    if (isMobileDevice()) {
      document.body.classList.add("mobile");
    }

    // Update on resize
    $(window).on("resize", function () {
      if (isMobileDevice()) {
        document.body.classList.add("mobile");
      } else {
        document.body.classList.remove("mobile");
      }
    });

    // Watch for new messages (when contact changes)
    // Use 2 second interval for better responsiveness while reducing server load
    let lastPhonenumber = null;
    let smartChipsLoaded = false;
    let lastMessageCount = 0;
    let lastMessageTimestamp = null;

    setInterval(function () {
      const currentPhonenumber = $("input[name='phonenumber']").val();

      if (currentPhonenumber && currentPhonenumber !== lastPhonenumber) {
        lastPhonenumber = currentPhonenumber;
        smartChipsLoaded = false;
        window.axiomSectionLoaded = { deal_pulse: false, client_dna: false, strat_path: false };
        lastMessageCount = 0; // Reset message count
        lastMessageTimestamp = null; // Reset timestamp

        // Only load AXIOM data if drawer is open (don't load on page load)
        if (axiomState.drawerOpen) {
          loadAXIOMData();
        }
      }

      // Fallback: Check for new messages periodically (only if drawer is open)
      // This ensures updates even if MutationObserver misses something
      if (
        axiomState.drawerOpen &&
        currentPhonenumber &&
        axiomState.currentLeadId
      ) {
        // Check both message count and last message timestamp for more reliable detection
        const chatContainer = $("#retorno");
        const currentMessageCount = chatContainer.find(".chat-others").length;

        // Also check for the most recent message timestamp
        let mostRecentTimestamp = null;
        chatContainer.find(".chat-others").each(function () {
          const $msg = $(this);
          const msgDate =
            $msg.find("[data-date], .msg_date, .chat-date").attr("data-date") ||
            $msg.find("[data-date], .msg_date, .chat-date").text();
          if (msgDate) {
            const timestamp = new Date(msgDate).getTime();
            if (!mostRecentTimestamp || timestamp > mostRecentTimestamp) {
              mostRecentTimestamp = timestamp;
            }
          }
        });

        // Update if message count increased OR if we have a new timestamp
        const hasNewMessage =
          currentMessageCount > lastMessageCount ||
          (mostRecentTimestamp && mostRecentTimestamp !== lastMessageTimestamp);

        if (hasNewMessage) {
          console.log("🔄 AXIOM: New message detected via periodic check", {
            count: currentMessageCount,
            lastCount: lastMessageCount,
            timestamp: mostRecentTimestamp,
            lastTimestamp: lastMessageTimestamp,
          });

          lastMessageCount = currentMessageCount;
          if (mostRecentTimestamp) {
            lastMessageTimestamp = mostRecentTimestamp;
          }

          clearTimeout(window.axiomPeriodicUpdateTimeout);
          window.axiomPeriodicUpdateTimeout = setTimeout(function () {
            var $dp = $("#dealPulseSection"), $cd = $("#clientDNASection"), $sp = $("#stratPathSection");
            if ($dp.length && !$dp.hasClass("axiom-collapsed")) loadDealPulse(axiomState.currentLeadId);
            setTimeout(function () {
              if ($cd.length && !$cd.hasClass("axiom-collapsed")) loadClientDNA(axiomState.currentLeadId);
            }, 200);
            setTimeout(function () {
              if ($sp.length && !$sp.hasClass("axiom-collapsed")) loadStratPath(axiomState.currentLeadId);
            }, 400);
            setTimeout(function () {
              loadSmartChips(axiomState.currentLeadId);
            }, 600);
          }, 500);
        } else if (lastMessageCount === 0 && currentMessageCount > 0) {
          // Initialize count on first check
          lastMessageCount = currentMessageCount;
          if (mostRecentTimestamp) {
            lastMessageTimestamp = mostRecentTimestamp;
          }
        }
      }

      // Load smart chips only if contact exists and not already loaded
      // Only load if there's a visible chat input (meaning chat is active)
      if (
        currentPhonenumber &&
        !smartChipsLoaded &&
        $("#textarea-chat").length > 0
      ) {
        // Delay smart chips loading to not block initial page load
        setTimeout(function () {
          const phoneCheck = $("input[name='phonenumber']").val();
          if (currentPhonenumber === phoneCheck) {
            loadSmartChips(currentPhonenumber);
            smartChipsLoaded = true;
          }
        }, 2000); // Load 2 seconds after page load
      }
    }, 2000); // Check every 2 seconds for better responsiveness

    // Listen for new incoming messages - Use MutationObserver instead of deprecated DOMNodeInserted
    // This is more performant and doesn't fire on every DOM change
    if (window.MutationObserver) {
      let lastMessageCount = 0;
      let lastUpdateTime = 0;

      const messageObserver = new MutationObserver(function (mutations) {
        // Check if a new message was added (only incoming messages from lead)
        let newIncomingMessage = false;
        let messageText = "";
        let hasAudio = false;
        let audioUrl = null;

        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              // Element node
              const $target = $(node);

              // Check if it's a new incoming message from the lead (chat-others)
              if (
                $target.hasClass("chat-others") ||
                $target.closest(".chat-others").length
              ) {
                const msgText =
                  $target.text() ||
                  $target.find(".chat-message-text, .msg_content").text() ||
                  "";

                if (msgText && msgText.trim()) {
                  newIncomingMessage = true;
                  messageText = msgText;

                  // Check for audio files
                  const audioElement = $target.find(
                    'audio, [data-type="audio"], [data-audio]',
                  );
                  if (audioElement.length) {
                    hasAudio = true;
                    audioUrl =
                      audioElement.attr("src") ||
                      audioElement.data("url") ||
                      audioElement.attr("data-audio");
                  }
                }
              }
            }
          });
        });

        // Update AXIOM features in real-time when new incoming message is detected
        if (
          newIncomingMessage &&
          axiomState.drawerOpen &&
          axiomState.currentLeadId
        ) {
          const now = Date.now();
          // Debounce to avoid multiple rapid calls (min 1 second between updates)
          if (now - lastUpdateTime > 1000) {
            lastUpdateTime = now;

            clearTimeout(window.axiomUpdateTimeout);
            window.axiomUpdateTimeout = setTimeout(function () {
              console.log(
                "🔄 AXIOM: Updating features in real-time after new message:",
                messageText.substring(0, 50),
              );

              var $dp = $("#dealPulseSection"), $cd = $("#clientDNASection"), $sp = $("#stratPathSection");
              if ($dp.length && !$dp.hasClass("axiom-collapsed")) loadDealPulse(axiomState.currentLeadId);
              setTimeout(function () {
                if ($cd.length && !$cd.hasClass("axiom-collapsed")) loadClientDNA(axiomState.currentLeadId);
              }, 200);
              setTimeout(function () {
                if ($sp.length && !$sp.hasClass("axiom-collapsed")) loadStratPath(axiomState.currentLeadId);
              }, 400);
              setTimeout(function () {
                loadSmartChips(axiomState.currentLeadId);
              }, 600);
            }, 500); // Wait 500ms after message detection before updating
          }

          // Check for FlowSync intent
          if (messageText) {
            detectFlowSyncIntent(messageText);
          }

          // Check for audio files
          if (hasAudio && audioUrl) {
            detectAudioMessage(null, audioUrl);
          }
        }
      });

      // Observe the chat container when it exists (delay to not block page load)
      setTimeout(function () {
        // Try multiple selectors to find the chat container
        const chatContainer = document.querySelector(
          '#retorno, .chat-container, .chat-messages, #chat-messages, [id*="retorno"]',
        );
        if (chatContainer) {
          console.log(
            "👁️ AXIOM: Observing chat container for real-time updates",
          );
          messageObserver.observe(chatContainer, {
            childList: true,
            subtree: true,
          });
        } else {
          console.warn("⚠️ AXIOM: Chat container not found, retrying...");
          // Retry after another 2 seconds
          setTimeout(function () {
            const retryContainer = document.querySelector(
              '#retorno, .chat-container, .chat-messages, #chat-messages, [id*="retorno"]',
            );
            if (retryContainer) {
              console.log("👁️ AXIOM: Chat container found on retry");
              messageObserver.observe(retryContainer, {
                childList: true,
                subtree: true,
              });
            }
          }, 2000);
        }
      }, 2000); // Wait 2 seconds after page load

      // Also listen for WebSocket message events if available
      // This is a fallback in case MutationObserver misses some messages
      if (typeof window.addEventListener !== "undefined") {
        // Listen for custom events that might be dispatched when messages arrive
        window.addEventListener("axiom-message-received", function (e) {
          if (axiomState.drawerOpen && axiomState.currentLeadId) {
            console.log("🔄 AXIOM: Updating features via custom event");
            clearTimeout(window.axiomUpdateTimeout);
            window.axiomUpdateTimeout = setTimeout(function () {
              var $dp = $("#dealPulseSection"), $cd = $("#clientDNASection"), $sp = $("#stratPathSection");
              if ($dp.length && !$dp.hasClass("axiom-collapsed")) loadDealPulse(axiomState.currentLeadId);
              if ($cd.length && !$cd.hasClass("axiom-collapsed")) loadClientDNA(axiomState.currentLeadId);
              if ($sp.length && !$sp.hasClass("axiom-collapsed")) loadStratPath(axiomState.currentLeadId);
              loadSmartChips(axiomState.currentLeadId);
            }, 500);
          }
        });
      }
    }
  });
})(jQuery);
