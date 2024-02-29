<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumnoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "type"=>"Alumnos",
            "attributes"=>[
                "nombre"=>$this->nombre,
                "direction"=>$this->direcction,
                "email"=>$this->email,
            ],
            "link"=>[
                "self"=> url('api/alumnos/' . $this->id)
            ]
        ];
    }
    public function with(Request $request)
    {
        return [
            "jsonapi"=>[
                "version"=>"1.0"
            ]
        ];
    }
}
