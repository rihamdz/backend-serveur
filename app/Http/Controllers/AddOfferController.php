<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class AddOfferController extends Controller
{
    public function addOfferFunction(){
        return view("addOfferView");
    }
    public function addOfferToDB(Request $request){
        $data = new Offer;
        $data->titel = $request->title;
        $data->description = $request->desc;
        $data->requirement = $request->req;
        $data->state = true;
        $imageReq = $request->image;
        if ( $imageReq != null ) {
            $imagename = time().".".$imageReq->getClientOriginalExtension();
            $request->image->move('offerImage',$imagename);
            $data->image = $imagename;
        }
        $data->save();
        return redirect()->back();
    }

    public function showOffers(){

        $data = Offer::all();
        return view('OffersView' , compact('data'));
    }

    public function deleteOffer($id){
        $data = Offer::find($id);
        $data->delete();
        return redirect()->back();
    }


    public function updateOffer($id){
        
        $offer = Offer::find($id);
        return view('offerUpdateView', compact('offer'));
    }

    public function editOfferInDB(Request $request , $id){
        $offer = Offer::find($id);
        $offer->titel = $request->title;
        $offer->description = $request->desc;
        $offer->requirement = $request->req;
   
        $offer->state = $request->status;
        
        $imageReq = $request->image;
        if ( $imageReq != null ) {
            $imagename = time().".".$imageReq->getClientOriginalExtension();
            $request->image->move('offerImage',$imagename);
            $offer->image = $imagename;
        }

        $offer->update();
        return redirect()->back();


    }

    


}
