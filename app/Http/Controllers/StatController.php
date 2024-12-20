<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DecesHop;
use App\Models\NaissHop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF; 
use Carbon\Carbon; 

class StatController extends Controller
{
    public function index(Request $request)
    {
        $sousadmin = Auth::guard('sous_admin')->user();
        $communeAdmin = $sousadmin->nomHop;
    
        // Récupérer le mois et l'année sélectionnés
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));
    
        // Compter le total des déclarations de naissance et de décès pour le mois sélectionné
        $naisshop = NaissHop::where('NomEnf', $communeAdmin)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->count();
    
        $deceshop = DecesHop::where('nomHop', $communeAdmin)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->count();
    
        // Récupérer les données pour les graphiques
        $naissData = NaissHop::where('NomEnf', $communeAdmin)
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('strftime("%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();
    
        $decesData = DecesHop::where('nomHop', $communeAdmin)
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('strftime("%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();
    
        // Remplir les données manquantes pour chaque mois
        $naissData = array_replace(array_fill(1, 12, 0), $naissData);
        $decesData = array_replace(array_fill(1, 12, 0), $decesData);
    
        $total = $naisshop + $deceshop;
    
        // Vérifier si le téléchargement en PDF est demandé
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('stat.pdf', compact('naisshop', 'deceshop', 'total', 'selectedMonth', 'selectedYear', 'naissData', 'decesData'));
            return $pdf->download('statistiques.pdf');
        }
    
        return view('stat.index', compact('naisshop', 'deceshop', 'total', 'selectedMonth', 'selectedYear', 'naissData', 'decesData'));
    }

    
    public function download()
    {
        // Définir la locale de Carbon en français
        Carbon::setLocale('fr');

        // Récupérer les données
        $naisshopCount = NaissHop::count(); // Compte des naissances
        $deceshopCount = DecesHop::count(); // Compte des décès

        // Récupérer le nom de l'hôpital
        $sousadmin = Auth::guard('sous_admin')->user();
        $hopitalName = $sousadmin->nomHop;

        // Récupérer les données par mois
        $naissData = NaissHop::selectRaw('strftime("%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();

        $decesData = DecesHop::selectRaw('strftime("%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();

        // Préparer les données pour le PDF
        $data = [
            'naisshopCount' => $naisshopCount,
            'deceshopCount' => $deceshopCount,
            'hopitalName' => $hopitalName,
            'naissData' => $naissData,
            'decesData' => $decesData,
        ];

        // Générer le PDF
        $pdf = PDF::loadView('stat.pdf', $data);

        // Retourner le PDF en téléchargement
        return $pdf->download('statistiques.pdf');
    }
}
