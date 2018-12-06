<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{

/**
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Development server",
 
 * )
 */
    
/**
 * @OA\Info(
 *     description="Pra casar API for wedding name Pra Casar",
 *     version="1.0.0",
 *     title="Api Wedding",
 *     termsOfService="",
 *     @OA\Contact(
 *         email="admin@approx.com.br"
 *     ),
 *     @OA\License(
 *         name="Pro Produtores",
 *         url="https://www.proprodutores.com.br/"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="User",
 *     description="Tag for Users services",
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Action",
 *     description="Action is responsible for the part of the wedding's expenses",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="ChromaImage",
 *     description="Tag for Chroma Image",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Adress",
 *     description="Adress for a lot of uses",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Event",
 *     description="Events of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Guest",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Invite",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Post",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="Song",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="TieBuy",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */

 /**
 * @OA\Tag(
 *     name="TiePiece",
 *     description="Guests of the Wedding",
 *     
 * )
 * 
 * 
 */


//Pivot Tables
/**
     * @OA\Schema(
     *     schema="EventGuest",
     *     required={"event_id", "guest_id", "confirmed"},
     *     @OA\Property(
     *         property="event_id",
     *         type="integer",
     *         example=5
     *     ),
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="JMF87676"
     *     ),
     *     @OA\Property(
     *         property="confirmed",
     *         type="tinyint",
     *         example=1     
     *     )
     * )
    */

/**
* @OA\Schema(
*     schema="PostLike",
*     required={"post_id", "guest_id"},
*     @OA\Property(
*         property="post_id",
*         type="integer",
*         example=5
*     ),
*     @OA\Property(
*         property="guest_id",
*         type="string",
*         example="JMF87676"
*     )
* )
*/    


/**
* @OA\Schema(
*     schema="PostAprove",
*     required={"aproved"},
*     @OA\Property(
*         property="aproved",
*         type="tinyint",
*         example=1
*     )
* )
*/
}
