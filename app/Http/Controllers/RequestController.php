<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    public function index()
    {
        try {
            $requests = Request::all();
            return response()->json($requests);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve requests'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(HttpRequest $request)
    {
        try {
            $validatedData = $request->validate([
                'full_name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',
                'priority' => 'required|in:1,2,3',
                'suma_necesara' => 'required|numeric',
                'center_of_cost_id' => 'required|exists:centers_of_cost,id',
                'detalii_plata' => 'required|string|max:255',
                'payment_method' => 'required|string|max:255',
                'card' => 'required|exists:cards,id',
                'observatii_financiar' => 'nullable|string',
                'observatii_solicitant' => 'nullable|string',
            ]);

            $validatedData['status'] = '1'; // Setăm implicit statusul la 'created' (1)

            $requestModel = Request::create($validatedData);
            return response()->json($requestModel, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create request'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $requestModel = Request::findOrFail($id);
            return response()->json($requestModel);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve request'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(HttpRequest $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'full_name' => 'sometimes|required|string|max:255',
                'contact_number' => 'sometimes|required|string|max:255',
                'priority' => 'sometimes|required|in:1,2,3',
                'suma_necesara' => 'sometimes|required|numeric',
                'center_of_cost_id' => 'sometimes|required|exists:centers_of_cost,id',
                'detalii_plata' => 'sometimes|required|string|max:255',
                'payment_method' => 'sometimes|required|string|max:255',
                'card' => 'sometimes|required|exists:cards,id',
                'status' => 'sometimes|required|in:1,2,3',
                'observatii_financiar' => 'sometimes|nullable|string',
                'observatii_solicitant' => 'sometimes|nullable|string',
            ]);

            $requestModel = Request::findOrFail($id);
            $requestModel->update($validatedData);
            return response()->json($requestModel);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to update request'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $requestModel = Request::findOrFail($id);
            $requestModel->delete();
            return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to delete request'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
