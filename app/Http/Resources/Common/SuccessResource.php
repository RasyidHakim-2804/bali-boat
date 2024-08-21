<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(
        private int $code        = JsonResponse::HTTP_OK,
        private ?string $message = null,
        private $data = null,
    )
    {
        parent::__construct((object)[]);
    }


    public function withResponse(Request $request, JsonResponse $response)
    {
        return $response->setStatusCode($this->code);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => 'success',

            $this->mergeWhen($this->message !== null, [
                'message' => $this->message
            ]),

            $this->mergeWhen($this->data != null, [
                'data' => $this->data
            ]),
        ];
    }

}