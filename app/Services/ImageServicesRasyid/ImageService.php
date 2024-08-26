<?php
namespace App\Services\ImageServicesRasyid;

use App\Contracts\Models\CanUploadImage;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Service class for handling image upload, update, and rollback operations.
 */
class ImageService
{
    /**
     * @var string The directory path where images are stored.
     */
    private $modelDirPath;

    /**
     * @var string The disk used for storing images.
     */
    private $modelDisk;

    /**
     * @var string|null The old image URL before update.
     */
    private $modelOldImagePath;

    /**
     * @var string The format of the image file.
     */
    private $format;

    /**
     * @var string|null The URL of the newly uploaded image.
     */
    private $newImagePath;

    /**
     * @var string|null The URL of the deleted image before rollback.
     */
    private $deletedImagePath;

    /**
     * @var string The directory path where deleted images are moved.
     */
    private $deletedDirectory = 'imageService/deleted/';

    public function __construct(CanUploadImage $model)
    {
        $this->modelDirPath          = $model->getDirPath();
        $this->modelDisk             = $model->getDisk();
        $this->modelOldImagePath      = $model->getOldImage();
    }

    //////////////////////////
    // public CRUD function //
    /////////////////////////

    public static function  collection(Collection|array $collection)
    {
        return new ImageServiceCollection($collection);
    }
    
    /**
     * Uploads an image and returns information about the uploaded file.
     *
     * @param UploadedFile $image The image file to upload.
     * @return object An object containing information about the uploaded file:
     *                - path: The path where the file is stored.
     *                - name: The generated name of the file.
     */
    public function upload(UploadedFile $image, ?string $to = null)
    {
        $this->validateType($image);

        $name = Str::ulid()->toBase32()  . '.' . $this->format;
        $path = $this->modelDirPath . $name;

        if ($to != null) {
            $customPath = trim( $to, '/') . '/';
            $path       = $customPath . $name;
        }

        $this->store($image, $path);
        $this->newImagePath = $path;

        return (object) [
            'path'  => $path,
            'name'  => $name,
        ];
    }

    /**
     * Updates the content with a new file upload.
     *
     * This method deletes any existing content and uploads a new file, returning information about the uploaded file.
     *
     * @param UploadedFile $content The new content file to upload.
     * @return object An object containing information about the newly uploaded file:
     *                - path: The path where the file is stored.
     *                - name: The generated name of the file.
     */
    public function update(UploadedFile $content, ?string $from = null, ?string $to = null)
    {
        $this->delete($from);
        return $this->upload($content, $to);
    }

    /**
     * Deletes the old image file if it exists.
     *
     * Checks if the old image URL exists in the specified disk.
     *
     * @return bool|null True if the file was moved to the deleted directory, null otherwise.
     */
    public function delete(?string $from = null): bool|null
    {
        $oldPath = $this->modelOldImagePath;

        if ($from) {
            $oldPath = $from;
        }

        if (Storage::disk($this->modelDisk)->exists($oldPath)) {

            $this->deletedImagePath = $this->deletedDirectory . $oldPath;
        
            return Storage::disk($this->modelDisk)->move(
                from: $oldPath,
                to: $this->deletedImagePath
            );
        }
        return null;
    }

    /**
     * Rolls back changes by deleting new image and restoring deleted image.
     */
    public function rollback()
    {
        if( isset($this->newImagePath) ) {
            Storage::disk($this->modelDisk)->delete($this->newImagePath);

            unset($this->newImagePath);
        }

        if ( isset($this->deletedImagePath) ) {

            Storage::disk($this->modelDisk)->move(
                from: $this->deletedImagePath,
                to: $this->modelOldImagePath
            );

            unset($this->deletedImagePath);
        }
    }

    public function __destruct()
    {
        if ( isset($this->deletedImagePath) ) {
            Storage::disk($this->modelDisk)->delete($this->deletedImagePath);
        }
    }

    /////////////
    //protected//
    /////////////

    protected function imagePreProcessing(UploadedFile $image)
    {   
        switch ($this->format) {
            case 'jpeg':
            case 'jpg':
                return Image::read($image)->scaleDown(1280, 720)->toJpeg(80);
                break;
            
            case 'png':
                return Image::read($image)->scaleDown(1280, 720)->toPng(false);
                break;
            
            case 'webp':
                return Image::read($image)->scaleDown(1280, 720)->toWebp(80);
                break;

            case 'gif':
                return Image::read($image)->scaleDown(1280, 720)->toGif(false);
                break;

            default:
                throw new Exception('Unknown image format');
                break;
        }
    }

    /////////////
    // private //
    /////////////

    private function validateType(UploadedFile $image)
    {
        $validator = Validator::make(
            data: ['image' => $image],
            rules: ['image' => 'image']
        );

        $validator->validate();

        $this->format = explode('/',$image->getMimeType())[1];
    }

    private function store($image, $path)
    {
        $image = $this->imagePreProcessing($image);

        Storage::disk($this->modelDisk)->put($path, $image);
    }

}