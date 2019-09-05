<?php
declare(strict_types = 1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnsplashController extends AuthorizeController
{
    public function __construct(Request $request)
    {
        if(!isset($_SESSION['access_token'])){
            $_SESSION['request_url'] = $request->path();
            $this->getAccessCode($request);
        }
    }

    /**
     * Get a single page from the list of all photos.
     *
     * @return JsonResponse
     */
    public function getCurrentUserProfile() : JsonResponse
    {
        //1. call getDetail with url of get request
        $result = json_decode($this->getDetails("me"));

        //2. return JSON response
        return response()->json($result);
    }

    /**
     * Retrieve the user profile
     *
     * @return JsonResponse
     */
    public function listPhotos() : JsonResponse
    {
        //1. call getDetail with url of get request
        $result = json_decode($this->getDetails("photos"));

        //2. return JSON response
        return response()->json($result);
    }

    /**
     * Get a single page from the list of all collections.
     *
     * @return JsonResponse
     */
    public function listCollections() : JsonResponse
    {
        //1. call getDetail with url of get request
        $result = json_decode($this->getDetails("collections"));

        //2. return JSON response
        return response()->json($result);
    }

    /**
     * Get request to third party url
     */
    public function getDetails(string $url) : string
    {
        try 
        {
            $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.unsplash.com/']);
            $headers = [
                'Authorization' => 'Bearer ' . $_SESSION['access_token'],        
                'Accept'        => 'application/json',
            ];
            $response = $client->request('GET', $url, [
                            'headers' => $headers
                        ]);
            $data = $response->getBody()->getContents();
            return $data;
        } catch (\Exception $ex) 
        {
            abort(404, 'Unable To Call Unsplash Api Request.'.$ex->getMessage());
        }
    }
}