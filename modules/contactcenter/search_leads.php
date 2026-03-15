<?php
/**
 * Local Lead Scraper with AI Enrichment
 * 
 * Finds business leads using Google Maps Places API (New)
 * Enriches leads with WhatsApp/mobile numbers using Gemini AI with Google Search
 * 
 * Usage: php search_leads.php "restaurants in São Paulo, SP, Brazil"
 */

// CONFIGURATION - Replace with your actual API keys
define('GOOGLE_MAPS_API_KEY', 'YOUR_MAPS_API_KEY');
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');

/**
 * Step 1: Discovery (Google Maps Places API New)
 * Finds businesses using Places API with FieldMask for cost optimization
 */
function findLeads($query, $max_results = 100) {
    $api_url = "https://places.googleapis.com/v1/places:searchText";
    
    // Request body
    $data = [
        'textQuery' => $query,
        'maxResultCount' => min($max_results, 20), // API max is 20 per request
        'languageCode' => 'pt-BR',
        'regionCode' => 'BR'
    ];
    
    // CRITICAL: FieldMask controls costs - only request what we need
    $headers = [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . GOOGLE_MAPS_API_KEY,
        'X-Goog-FieldMask: places.id,places.displayName,places.formattedAddress,places.nationalPhoneNumber,places.internationalPhoneNumber,places.websiteUri,places.businessStatus'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($curl_error) {
        return ['error' => 'Maps API CURL Error: ' . $curl_error];
    }
    
    if ($http_code !== 200) {
        $decoded = json_decode($response, true);
        $error_msg = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'HTTP ' . $http_code;
        return ['error' => 'Maps API Error: ' . $error_msg];
    }
    
    $decoded = json_decode($response, true);
    
    if (!isset($decoded['places']) || empty($decoded['places'])) {
        return [];
    }
    
    // Filter only OPERATIONAL businesses
    $operational_leads = [];
    foreach ($decoded['places'] as $place) {
        if (isset($place['businessStatus']) && $place['businessStatus'] === 'OPERATIONAL') {
            $operational_leads[] = $place;
        }
    }
    
    // Handle pagination if needed
    $all_results = $operational_leads;
    $next_page_token = isset($decoded['nextPageToken']) ? $decoded['nextPageToken'] : null;
    
    $pages_needed = ceil($max_results / 20) - 1;
    for ($page = 0; $page < $pages_needed && $next_page_token; $page++) {
        sleep(2); // Required delay between requests
        
        $data['pageToken'] = $next_page_token;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $page_response = curl_exec($ch);
        curl_close($ch);
        
        $page_decoded = json_decode($page_response, true);
        if (isset($page_decoded['places']) && !empty($page_decoded['places'])) {
            foreach ($page_decoded['places'] as $place) {
                if (isset($place['businessStatus']) && $place['businessStatus'] === 'OPERATIONAL') {
                    $all_results[] = $place;
                }
            }
            $next_page_token = isset($page_decoded['nextPageToken']) ? $page_decoded['nextPageToken'] : null;
        } else {
            break;
        }
    }
    
    return array_slice($all_results, 0, $max_results);
}

/**
 * Step 2: Analysis & Enrichment (Gemini + Google Search)
 * Uses Gemini with googleSearchRetrieval to find WhatsApp/mobile numbers
 */
function enrichLead($businessName, $city, $currentPhone, $website) {
    // Use gemini-flash-latest which is available in v1beta API
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY;
    
    // Build research prompt
    $prompt = "Research this business: {$businessName} in {$city}.\n";
    $prompt .= "Current phone: {$currentPhone}\n";
    $prompt .= "Website: {$website}\n\n";
    $prompt .= "Task: Find their official WhatsApp or Mobile number (mobile numbers in Brazil start with '9' after area code).\n";
    $prompt .= "Check Instagram bio, Facebook page, Google My Business, and business directories.\n\n";
    $prompt .= "Return ONLY valid JSON with these exact keys:\n";
    $prompt .= "{\n";
    $prompt .= '  "whatsapp_number": "phone number in format (XX) 9XXXX-XXXX or null",' . "\n";
    $prompt .= '  "social_media": "Instagram or Facebook URL or null",' . "\n";
    $prompt .= '  "confidence_level": "high|medium|low"' . "\n";
    $prompt .= "}\n\n";
    $prompt .= "If no WhatsApp/mobile number found, set whatsapp_number to null. Do not invent numbers.";
    
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'tools' => [
            [
                'googleSearchRetrieval' => []
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'responseMimeType' => 'application/json', // Force JSON response
            'maxOutputTokens' => 500
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        error_log("Gemini API Error: HTTP {$http_code} - {$response}");
        return [
            'whatsapp_number' => null,
            'social_media' => null,
            'confidence_level' => 'low'
        ];
    }
    
    $jsonResponse = json_decode($response, true);
    
    // Extract text from Gemini response
    if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
        $textResponse = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
        
        // Try to parse JSON from response
        // Sometimes Gemini wraps JSON in markdown code blocks
        $textResponse = preg_replace('/```json\s*/', '', $textResponse);
        $textResponse = preg_replace('/```\s*/', '', $textResponse);
        $textResponse = trim($textResponse);
        
        $enrichment = json_decode($textResponse, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($enrichment)) {
            return [
                'whatsapp_number' => $enrichment['whatsapp_number'] ?? null,
                'social_media' => $enrichment['social_media'] ?? null,
                'confidence_level' => $enrichment['confidence_level'] ?? 'low'
            ];
        }
    }
    
    // Fallback if JSON parsing fails
    return [
        'whatsapp_number' => null,
        'social_media' => null,
        'confidence_level' => 'low'
    ];
}

/**
 * Format phone number for display
 */
function formatPhone($phone) {
    if (empty($phone)) {
        return '';
    }
    
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);
    
    // Format Brazilian phone: (XX) XXXX-XXXX or (XX) 9XXXX-XXXX
    if (strlen($phone) >= 10) {
        // Remove country code if present (55 for Brazil)
        if (substr($phone, 0, 2) === '55' && strlen($phone) > 10) {
            $phone = substr($phone, 2);
        }
        
        if (strlen($phone) === 10) {
            // Landline: (XX) XXXX-XXXX
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
        } elseif (strlen($phone) === 11) {
            // Mobile: (XX) 9XXXX-XXXX
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
        }
    }
    
    return $phone;
}

/**
 * Extract city from address
 */
function extractCity($address) {
    if (empty($address)) {
        return '';
    }
    
    $parts = explode(',', $address);
    if (count($parts) >= 2) {
        return trim($parts[count($parts) - 2]);
    }
    
    return '';
}

// --- MAIN EXECUTION ---

// Get query from command line or use default
$query = isset($argv[1]) ? $argv[1] : "restaurants in São Paulo, SP, Brazil";
$max_results = isset($argv[2]) ? (int)$argv[2] : 20;

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>Lead Scraper Results</title>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .confidence-high { color: green; font-weight: bold; }
    .confidence-medium { color: orange; }
    .confidence-low { color: red; }
    .status { padding: 5px 10px; border-radius: 3px; }
</style>\n";
echo "</head>\n<body>\n";

echo "<h2>🔍 Searching for: {$query}</h2>\n";
echo "<p>Max results: {$max_results}</p>\n";

// Step 1: Find leads
echo "<h3>Step 1: Finding leads via Google Places API...</h3>\n";
$places = findLeads($query, $max_results);

if (isset($places['error'])) {
    die("<p style='color:red;'>Error: " . htmlspecialchars($places['error']) . "</p></body></html>");
}

if (empty($places)) {
    die("<p>No operational businesses found.</p></body></html>");
}

echo "<p>Found " . count($places) . " operational businesses.</p>\n";

// Step 2: Enrich leads with Gemini
echo "<h3>Step 2: Enriching leads with Gemini AI...</h3>\n";
$enriched_leads = [];
$processed = 0;

foreach ($places as $place) {
    $processed++;
    $name = isset($place['displayName']['text']) ? $place['displayName']['text'] : 'Unknown';
    $address = $place['formattedAddress'] ?? '';
    $city = extractCity($address);
    $phone = formatPhone($place['internationalPhoneNumber'] ?? $place['nationalPhoneNumber'] ?? '');
    $website = $place['websiteUri'] ?? '';
    
    echo "<p>Processing {$processed}/" . count($places) . ": {$name}...</p>\n";
    flush();
    
    // Enrich with Gemini
    $enrichment = enrichLead($name, $city, $phone, $website);
    
    // Build CRM-ready lead
    $lead = [
        'lead_name' => $name,
        'address' => $address,
        'google_phone' => $phone,
        'whatsapp_enriched' => $enrichment['whatsapp_number'] ?? null,
        'website' => $website,
        'social_media' => $enrichment['social_media'] ?? null,
        'source' => 'Google Maps + Gemini AI',
        'status' => 'New',
        'confidence_level' => $enrichment['confidence_level'] ?? 'low'
    ];
    
    $enriched_leads[] = $lead;
    
    // Rate limiting: sleep between AI requests
    if ($processed < count($places)) {
        usleep(500000); // 0.5 seconds
    }
}

// Step 3: Display results
echo "<h3>Step 3: Results (CRM Ready)</h3>\n";

echo "<table>\n";
echo "<tr>\n";
echo "<th>Lead Name</th>\n";
echo "<th>Address</th>\n";
echo "<th>Google Phone</th>\n";
echo "<th>WhatsApp/Mobile</th>\n";
echo "<th>Website</th>\n";
echo "<th>Social Media</th>\n";
echo "<th>Confidence</th>\n";
echo "<th>Source</th>\n";
echo "<th>Status</th>\n";
echo "</tr>\n";

foreach ($enriched_leads as $lead) {
    $confidence_class = 'confidence-' . $lead['confidence_level'];
    echo "<tr>\n";
    echo "<td><strong>" . htmlspecialchars($lead['lead_name']) . "</strong></td>\n";
    echo "<td>" . htmlspecialchars($lead['address']) . "</td>\n";
    echo "<td>" . htmlspecialchars($lead['google_phone']) . "</td>\n";
    echo "<td>" . ($lead['whatsapp_enriched'] ? htmlspecialchars($lead['whatsapp_enriched']) : '<span style="color:gray;">N/A</span>') . "</td>\n";
    echo "<td>" . ($lead['website'] ? '<a href="' . htmlspecialchars($lead['website']) . '" target="_blank">' . htmlspecialchars($lead['website']) . '</a>' : 'N/A') . "</td>\n";
    echo "<td>" . ($lead['social_media'] ? '<a href="' . htmlspecialchars($lead['social_media']) . '" target="_blank">' . htmlspecialchars($lead['social_media']) . '</a>' : 'N/A') . "</td>\n";
    echo "<td><span class=\"{$confidence_class}\">" . strtoupper($lead['confidence_level']) . "</span></td>\n";
    echo "<td>" . htmlspecialchars($lead['source']) . "</td>\n";
    echo "<td><span class=\"status\" style=\"background-color:#4CAF50;color:white;\">" . htmlspecialchars($lead['status']) . "</span></td>\n";
    echo "</tr>\n";
}

echo "</table>\n";

// Save to JSON file
$json_output = json_encode($enriched_leads, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents('leads_export.json', $json_output);

echo "<h3>✅ Export</h3>\n";
echo "<p>Results saved to <strong>leads_export.json</strong></p>\n";
echo "<pre style='background:#f5f5f5;padding:15px;border-radius:5px;overflow:auto;max-height:400px;'>" . htmlspecialchars($json_output) . "</pre>\n";

echo "</body>\n</html>\n";
?>
