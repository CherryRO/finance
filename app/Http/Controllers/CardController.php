<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CardController extends Controller
{
  public function index()
  {
    try {
      $cards = Card::all();
      return response()->json($cards);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to retrieve cards'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function store(Request $request)
  {
    try {
      $validatedData = $request->validate([
        'bank' => 'required|string|max:255',
        'cardholder_name' => 'required|string|max:255',
        'first_four_digits' => 'required|string|size:4',
        'last_four_digits' => 'required|string|size:4',
        'expiry_date' => 'required|string|max:5',
        'status' => 'required|in:1,2,3,4,5',
        'observatii' => 'nullable|string',
      ]);

      $card = Card::create($validatedData);
      return response()->json($card, Response::HTTP_CREATED);
    } catch (ValidationException $e) {
      return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to create card'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show($id)
  {
    try {
      $card = Card::findOrFail($id);
      return response()->json($card);
    } catch (ModelNotFoundException $e) {
      return response()->json(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to retrieve card'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $validatedData = $request->validate([
        'bank' => 'sometimes|required|string|max:255',
        'cardholder_name' => 'sometimes|required|string|max:255',
        'first_four_digits' => 'sometimes|required|string|size:4',
        'last_four_digits' => 'sometimes|required|string|size:4',
        'expiry_date' => 'sometimes|required|string|max:5',
        'status' => 'sometimes|required|in:1,2,3,4,5',
        'observatii' => 'sometimes|nullable|string',
      ]);

      $card = Card::findOrFail($id);
      $card->update($validatedData);
      return response()->json($card);
    } catch (ValidationException $e) {
      return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (ModelNotFoundException $e) {
      return response()->json(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to update card'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function destroy($id)
  {
    try {
      $card = Card::findOrFail($id);
      $card->delete();
      return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    } catch (ModelNotFoundException $e) {
      return response()->json(['error' => 'Card not found'], Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to delete card'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  // Funcția nouă pentru obținerea cardurilor mascate
  public function getMaskedCards()
  {
    try {
      $cards = Card::all(['id', 'first_four_digits', 'last_four_digits']);
      $maskedCards = $cards->map(function ($card) {
        return [
          'id' => $card->id,
          'card_number' => $card->first_four_digits . ' **** **** ' . $card->last_four_digits,
        ];
      });
      return response()->json($maskedCards);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Unable to retrieve masked cards'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
