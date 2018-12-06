<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Address extends Model
{
    /**
     * @OA\Schema(
     *     schema="Adress",
     *     required={"id", "street", "number", "neighborhood", "city", "state", "cep"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="street",
     *         type="string",
     *         example="Av dos Andradas"
     *     ),
     *     @OA\Property(
     *         property="number",
     *         type="string",
     *         example="79A"  
     *     ),
     *     @OA\Property(
     *         property="neighborhood",
     *         type="string",
     *         example="Centro"
     *     ),
     *     @OA\Property(
     *         property="city",
     *         type="string",
     *         example="Belo Horizonte"
     *     ),
     *     @OA\Property(
     *         property="state",
     *         type="string",
     *         example="Minas Gerais"
     *              
     *     ),
     *     @OA\Property(
     *         property="cep",
     *         type="string",
     *         example="68490565"
     *     ),
     *     @OA\Property(
     *         property="complement",
     *         type="date",
     *         example="Esquina com Tamoios"
     *     ),
     * )
    */    
    protected $fillable = ['street','number','neighborhood','city','state','cep','complement'];
}
