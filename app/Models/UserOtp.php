<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;
  
class UserOtp extends Model
{
    use HasFactory;
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = ['user_id', 'otp', 'expire_at'];
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendSMS($receiverNumber)
    {
        $message = "Login OTP is ".$this->otp;
    
        try {

            $yoururl= "https://2factor.in/API/V1/4ebe5d1e-7bce-11ef-8b17-0200cd936042/SMS/$receiverNumber/$this->otp/OTP1";
            $response = Http::get($yoururl);

            $post = $response->json();
   
            if ($post['Status'] == 'Success') {
                info('SMS Sent Successfully.');
                return ['status' => 'success', 'message' => 'OTP has been sent to your mobile number.'];
            } else {
                info('Error: ' . $post['Details']);
                return ['status' => 'error', 'message' => $post['Details']];
            }
        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}