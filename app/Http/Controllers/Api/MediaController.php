<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Expert;
use App\Models\Api\FAQ;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\Media;
use App\Models\Api\News;
use App\Models\Api\Obituary;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\PublicHoliday;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\RoadAssistance;
use App\Models\Api\SalesRequests;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use App\Traits\HITrait;
use File;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\NetworkHelper;
use App\Helpers\ResponseHelper;
use Validator;
use Intervention\Image\ImageManagerStatic as Image;

class MediaController extends Controller
{
    use HITrait;
	/**
     * Upload Image
	 * @param Request $request
	 * @return \stdClass
	 */
    public function upload(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('media_type')){
            return parent::return_error("Missing Media Type", 401, 'messages.missing_parameter');
        }
        if(!Input::has('meta_name')){
            return parent::return_error("Missing Meta Name", 401, 'messages.missing_parameter');
        }
        $type_possible_values = ['voices','images','videos','documents'];
        if(!in_array($request->media_type,$type_possible_values)){
            return parent::return_error("Media Type Wrong Value", 402, 'messages.invalid_parameters');
        }
        $meta_possible_values = ['items'];
        if(!in_array($request->meta_name,$meta_possible_values)){
            return parent::return_error("Meta Name Wrong Value", 402, 'messages.invalid_parameters');
        }

        $fileName = null;
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . $user->id;
        // check if a file exist and is valid.
        if($request->hasFile('file')) {
            if($request->file('file')->isValid()) {
                $file = $request->file('file');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                if (!File::isDirectory($destinationDir)) {
                    File::makeDirectory($destinationDir, 0777, true, true);
                    $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/'.$request->media_type . DIRECTORY_SEPARATOR . $user->id.'/'.$request->meta_name;
                    if (!File::isDirectory($destinationDir)) {
                        File::makeDirectory($destinationDir, 0777, true, true);
                    }
                }else{
                    $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/'.$request->media_type . DIRECTORY_SEPARATOR . $user->id.'/'.$request->meta_name;
                    if (!File::isDirectory($destinationDir)) {
                        File::makeDirectory($destinationDir, 0777, true, true);
                    }else{
                        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/'.$request->media_type . DIRECTORY_SEPARATOR . $user->id.'/'.$request->meta_name;
                    }
                }
                     
                $resizedImage = Image::make($file)->resize(1024, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('jpg', 90);
                $resizedImage->save($destinationDir . DIRECTORY_SEPARATOR . $fileName);
                list($width, $height) = getimagesize($destinationDir . DIRECTORY_SEPARATOR . $fileName);

                // $file->move($destinationDir, $fileName);
                $this->correctImageOrientation($destinationDir.'/'.$fileName);
                // $exif = @exif_read_data($destinationDir.'/'.$fileName);
                // $width = $exif['COMPUTED']['Width'];
                // $height = $exif['COMPUTED']['Height'];
                // list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
                $media = Media::create([
                    'meta_id' => null,
                    'meta_name' => $request->meta_name,
                    'user_id' => $user->id,
                    'media_type' => $request->media_type,
                    'media_name' => $fileName,
                    'height' => $height,
                    'width' => $width,
                ]);
            }else{
                return parent::return_error("Invalid Image", 402, 'messages.invalid_parameters');
            }
        }else{
            return parent::return_error("Missing Image", 401, 'messages.missing_parameter');
        }

        return response(['media_id' => $media->id,'meta_name' => $request->meta_name,'media_url' => secure_url('public/uploads/images/'.$user->id.'/'.$request->meta_name.'/'.$fileName)], 200);
    }

    /**
     * Delete Image
     * @param Request $request
     * @return \stdClass
     */
    public function delete(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('media_id')){
            return parent::return_error("Missing Media ID", 401, 'messages.missing_parameter');
        }
        if(!$media = Media::whereUserId($user->id)->whereId($request->media_id)->first()){
            return parent::return_error("Invalid Media", 402, 'messages.invalid_parameters');
        }

        $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/'.$media->media_type  . DIRECTORY_SEPARATOR . $user->id . DIRECTORY_SEPARATOR . $media->meta_name . DIRECTORY_SEPARATOR . $media->getMediaName();
        File::delete($filePath);
        $media->delete();
        return response(['status' => 'Media deleted successfully'], 200);
    }
}
