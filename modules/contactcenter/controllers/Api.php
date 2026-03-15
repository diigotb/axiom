<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Contact Center API", version="1.0")
 * @OA\SecurityScheme(
 *     securityScheme="auth",
 *     type="apiKey",
 *     in="header",
 *     name="AXIOM-Authorization",
 *     bearerFormat="JWT",
 *     description="Token de autenticação no cabeçalho AXIOM-Authorization"
 * )
 */
class Api extends CI_Controller
{

    private $token_header = null;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');
        $this->token_header = get_option('contactcenter_api_bearer_token');
        $this->session->sess_destroy();
    }

    private function validate_token($token)
    {
        $token = $this->input->get_request_header('AXIOM-Authorization');
        if ($token != $this->token_header) {
            $this->output->set_status_header(401);
            $this->output->set_output(json_encode(['error' => 'Unauthorized']));
            return false;
        }
        return true;
    }

    private function validate_required_fields($data, $required_fields) {
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                $missing_fields[] = $field;
            }
        }
        if (!empty($missing_fields)) {
            $this->output->set_status_header(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation failed: missing required fields.',
                'missing_fields' => $missing_fields,                
            ], JSON_PRETTY_PRINT);
            return false;
        }
        return true;
    }

    private function validate_empty_fields($data, $required_fields) {
        $empty_fields = [];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $empty_fields[] = $field;
            }
        }
        if (!empty($empty_fields)) {
            $this->output->set_status_header(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation failed: empty required fields.',
                'empty_fields' => $empty_fields,
            ], JSON_PRETTY_PRINT);
            return false;
        }
        return true;
    }

    private function validate_mediatype_format($mediatype) {
        $allowed_mediatypes = ['image', 'video', 'document'];
        if (!in_array($mediatype, $allowed_mediatypes)) {
            $this->output->set_status_header(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid media type. Allowed values: image, video, document.'
            ], JSON_PRETTY_PRINT);
            return false;
        }
        return true;
    }

    private function clean_phone_number($phone_number) {
        return preg_replace('/[^0-9]/', '', $phone_number);
    }

    /**
     * @OA\Post(
     *     path="/contactcenter/api/text",
     *     tags={"Messages"},
     *     summary="Send a text message",
     *     security={{"auth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phonenumber", "message"},
     *             @OA\Property(property="phonenumber", type="int", example="5511992115620"),
     *             @OA\Property(property="message", type="string", example="Hello World!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Text sent successfully"),
     *     @OA\Response(response=400, description="Missing required fields or invalid JSON format"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function text() {
        if (!$this->validate_token($this->input->get_request_header('Authorization'))) {
            return;
        }
    
        header('Content-Type: application/json');
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid JSON format. Please check the JSON structure and try again.'
            ], JSON_PRETTY_PRINT);
            return;
        }
    
        $required_fields = ['phonenumber', 'message'];
    
        if (!$this->validate_required_fields($data, $required_fields)) {
            return;
        }   

        if (!$this->validate_empty_fields($data, $required_fields)) {
            return;
        }   

        $data['phonenumber'] = $this->clean_phone_number($data['phonenumber']);
        
        $result = $this->contactcenter_model->send_api_message($data, "text");      

        echo json_encode([
            "status" => $result["error"] ? 'error' : 'success',
            "message" => $result["response"]
        ], JSON_PRETTY_PRINT);
    }

    /**
     * @OA\Post(
     *     path="/contactcenter/api/media",
     *     tags={"Media"},
     *     summary="Send a media message",
     *     security={{"auth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phonenumber", "mediatype", "url"},
     *             @OA\Property(property="phonenumber", type="int", example="5517992014550"),
     *             @OA\Property(property="mediatype", type="string", example="image, video or document"),
     *             @OA\Property(property="url", type="string", example="https://example.com/media.png"),
     *             @OA\Property(property="caption", type="string", example="This is an example caption")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Media sent successfully"),
     *     @OA\Response(response=400, description="Missing required fields or invalid JSON format"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function media() {
        if (!$this->validate_token($this->input->get_request_header('Authorization'))) {
            return;
        }
    
        header('Content-Type: application/json');
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid JSON format. Please check the JSON structure and try again.'
            ], JSON_PRETTY_PRINT);
            return;
        }
    
        $required_fields = ['phonenumber', 'mediatype', 'url'];
    
        if (!$this->validate_required_fields($data, $required_fields)) {
            return; 
        }

        if (!$this->validate_empty_fields($data, $required_fields)) {
            return; 
        }

        if (!$this->validate_mediatype_format($data['mediatype'])) {
            return;
        }

        $data['phonenumber'] = $this->clean_phone_number($data['phonenumber']);
    
        $data['caption'] = isset($data['caption']) ? $data['caption'] : '';        
    
        $result = $this->contactcenter_model->send_api_message($data, "media");

        echo json_encode([
            "status" => $result["error"] ? 'error' : 'success',
            "message" => $result["response"]
        ], JSON_PRETTY_PRINT);
    }
}
