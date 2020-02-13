<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mycloud\cloudModel;
use Auth;

class cloudController extends Controller
{
    public function getList(Request $req)
    {/***********FORMATO RECORD***********

        DESCRIZIONE DEL RECORD

        ID              id univoco del record
        id_owner        username del proprietario
        tipo_record     FILE | CARTELLA | SETTING
        val1            assume significato diverso in funzione del valore di 'tipo_record' -> vedi tabella seguente
        val2            assume significato diverso in funzione del valore di 'tipo_record' -> vedi tabella seguente
        val3            assume significato diverso in funzione del valore di 'tipo_record' -> vedi tabella seguente
        val4            assume significato diverso in funzione del valore di 'tipo_record' -> vedi tabella seguente
        val5            assume significato diverso in funzione del valore di 'tipo_record' -> vedi tabella seguente

        SIGNIFICATO DI 'val1-5' IN FUNZIONE DI 'tipo_record'

        `tipo_record` == 'FILE'
            `val1` ->   nome del file visualizzato all'utente. Alla creazione del record esso assume il valore originale del file caricato. L'utente può modificarne il valore
            `val2` ->   nome del file vero e proprio storizzato dentro storage/app/cloudDocuments (archivio di questo server)
            `val3` ->   percorso (path) del file all'interno del cloud dell'utente
            `val4` ->   dimensione del file in byte
            `val5` ->   id del padre
        `tipo_record` == 'DIR'
            `val1` ->   nome della cartella visualizzata all'utente.
            `val2` ->   non usata
            `val3` ->   percorso (path) della cartella all'interno del cloud dell'utente
            `val4` ->   dimensione dei file contenuti all'interno espressi in byte
            `val5` ->   id del padre
        `tipo_record` == 'SETTING'
            `val1` ->   spazio espresso in byte concesso all'utente all'interno del suo cloud
            `val2` ->   numero massimo di cartelle che l'utente può creare
            `val3` ->   numero massimo di file che l'utente può incamerare all'interno di una cartella
            `val4` ->   non utilizzato
            `val5` ->   non utilizzato
        */
        $id_path="0";//di default elenchiamo tutti gli elementi della root
        if(isset($req->id_path))
        { //se l'utente specifica un percorso ricerchiamo l'id del padre
            $id_path=$req->id_path;
        }
        //scarichiamo l'elenco delle cartelle
        $dlist=cloudModel::where('id_owner',Auth::user()->name)
                        ->where('tipo_record','DIR')
                        ->where('val5',$id_path)
                        ->orderBy('val1','asc')
                        ->get();
        //scarichiamo l'elenco dei file
        $flist=cloudModel::where('id_owner',Auth::user()->name)
                        ->where('tipo_record','FILE')
                        ->where('val5',$id_path)
                        ->orderBy('val1','asc')
                        ->get();

        return response()->json(['new_id_path' => $id_path, 'dlist' => $dlist , 'flist' => $flist],200);
    }
    public function primaVoltaVerif()
    {
        //verifico se questo è il primo upload per l'utente corrente
        $models= cloudModel::where('id_owner',Auth::user()->name)->take(2)->get();
        if(count($models)==0) //l'utente non ha mai fatto upload prima di adesso
        {
            //creo i settaggi di fault per l'utente
            $setting=new cloudModel;
            $setting->id_owner=Auth::user()->name;
            $setting->tipo_record="SETTING";
            $setting->val1=env('DEFAULT_MAX_BYTES_PER_USER', 20000000000);
            $setting->val2=env('DEFAULT_MAX_DIR_PER_USER', 100);
            $setting->val3=env('DEFAULT_MAX_FILE_PER_DIR', 200);
            $setting->save();  //salvo i settaggi di default

            //creo la cartella home
            $setting=new cloudModel;
            $setting->id_owner=Auth::user()->name;
            $setting->tipo_record="DIR";
            $setting->val1="home";
            $setting->val3="/";
            $setting->val4="0";
            $setting->val5="-1";
            $setting->save();  //salvo la cartella home
        }
    }
    public function upload( Request $req)
    {
        //NB: per ovviare all'exception di post too large ho modificato nel file /etc/php/7.2/apache2/php.ini
        //i seguenti campi:

        $this->primaVoltaVerif();//verifico se questo è il primo upload per l'utente corrente

        //Salvo nello store i file e ne registro i record sulla tabella cloud
        $files=$req->file('files');
        $n=0;$totSize=0;
        foreach($files as $key => $value)
        {
            $newModel= new cloudModel;
            $newModel->id_owner=Auth::user()->name;
            $newModel->tipo_record="FILE";
            $newModel->val1=$value->getClientOriginalName();//nome originale del file;
            $newModel->val2=$value->store('cloudDocuments');//nome del file all'interno dell'archivio
            $newModel->val3=$req->path;//percorso del file nel cloud
            $newModel->val4=$value->getSize();//dimensione del file
            $newModel->val5=$req->id_path;//id della directory padre

            $newModel->save();
            $n++;
            $totSize+=intval($value->getSize());
        }

        //Calcolo la dimensione totale contenuta nella tabella e ne aggiorno il record e faccio lo stesso con le tabelle padre via via fino alla root
        $pivot=$req->id_path;
        $wd=0; //watchdog
        while( $wd++<300 )
        {
            $totBytes=0;
            $totCalc= cloudModel::where('id_owner',Auth::user()->name) //carico tutti gli elementi contenuti all'interno della radice id_padre
                                ->where('val5',$pivot)
                                ->get();
            foreach($totCalc as $row) //eseguo la somma di tutti i valori
            {
                $totBytes += intval($row['val4']);
            }
            //aggiorno il record
            cloudModel::where('id',$pivot)->update(['val4' => $totBytes]);
            if($pivot=='-1' || $pivot==='-1') $wd=400;
            else $pivot=(cloudModel::where('id',$pivot)->first())['val5'];
        }
        //ritorno il totale dei file uloadati
        return response()->json(['total_files_uploaded' => $n,
                                 'total_size_uploaded' => $totSize
                                ],200);
    }
    public function folderCreate(Request $req)
    {/*`tipo_record` == 'DIR'
            `val1` ->   nome della cartella visualizzata all'utente.
            `val2` ->   non usata
            `val3` ->   percorso (path) della cartella all'interno del cloud dell'utente
            `val4` ->   dimensione dei file contenuti all'interno espressi in byte
            `val5` ->   id del padre*/
        
        $this->primaVoltaVerif();//verifico se questo è il primo upload per l'utente corrente
        //return response()->json([],200);
        $newModel= new cloudModel;
        $newModel->id_owner=Auth::user()->name;
        $newModel->tipo_record="DIR";
        $newModel->val1=$req->folderName;
        $newModel->val2="";
        $newModel->val3="";
        $newModel->val4="0";
        $newModel->val5=$req->id_path;
        $newModel->save();
        return response()->json([],200);
    }
    public function goUp(Request $req)
    {//ritorna il padre di $req->id_path
        //return response()->json(['id_path' => $req->id_path],200);
        $item=cloudModel::where('id_owner',Auth::user()->name)
                        ->where('id',$req->id_path)
                        ->first();

        return response()->json(['id_path' => $item->val5],200);
    }
    public function getPath(Request $req)
    {//ritorna un array di tutti i padri di $req->id_path

        $path=array();
        $pivot=$req->id_path;
        $w=0;
        //return response()->json(['id_path' => $req->id_path],200);
        while($pivot!=="0" && $w++ < 300)
        {
            $item=cloudModel::where('id',$pivot)->first();
            if($item==null) $w=300;
            else{
                $path[]=$item;
                $pivot=$item['val5'];
            }
        }
        return response()->json(['path' => array_reverse($path)],200);
    }
    public function getSelectedFilesRecursiveStat(Request $req) //informazioni statistiche dei file selezionati tenendo conto dei contenuti delle directory  
    {
        $records=$req->json()->all();
        $totalFiles=0;
        $totalDirs=0;
        $totalSize=0;
        $idSelected=[];
        
        foreach($records as $item){
            
            if($item['tipo_record']==='DIR'){                
                $totalDirs++;
                $ret= $this->getDirRecStat($item['id']);
                $totalDirs+=intval($ret['totalDirs']);
                $totalFiles+=intval($ret['totalFiles']);
                $totalSize+=intval($ret['totalSize']);
                foreach($ret['idSelected'] as $item) $idSelected[]=$item;
            }
            else{
                $totalFiles++;
                $totalSize+=intval($item['val4']);
            }
            $idSelected[]=$item['id'];
        }
        //return $item;
        return response()->json(['total' => $totalFiles + $totalDirs , 
                                 'totalFiles' => $totalFiles,
                                 'totalDirs' => $totalDirs,
                                 'totalSize' => $totalSize,                                   
                                 'idSelected'=> $idSelected,  //id dei file da copiare o tagliare
        ],200);
    }
    private function getDirRecStat($id_padre) //funzione ricorsiva per le informazioni statistiche dei file e delle directory selezionati   
    {
        $n=0;
        $totalFiles=0;
        $totalDirs=0;
        $totalSize=0;
        $idSelected=[];

        $dlist=cloudModel::where('id_owner',Auth::user()->name)
                        ->where('tipo_record','DIR')
                        ->where('val5',$id_padre)
                        ->orderBy('val1','asc')
                        ->get();
        //scarichiamo l'elenco dei file
        $flist=cloudModel::where('id_owner',Auth::user()->name)
                        ->where('tipo_record','FILE')
                        ->where('val5',$id_padre)
                        ->orderBy('val1','asc')
                        ->get();

        foreach($flist as $item){
                $totalFiles++;
                $totalSize+=intval($item['val4']);
                $idSelected[]=$item['id'];
        }
        foreach($dlist as $item){
                $totalDirs++;
                $r=$this->getDirRecStat($item['id']);
                $totalSize += intval($r['totalSize']);
                $totalDirs += intval($r['totalDirs']);
                $totalFiles += intval($r['totalFiles']);
                foreach($r['idSelected'] as $item) $idSelected[]=$item;
        }
       
       
        return ['total' => $totalFiles + $totalDirs , 
                'totalFiles' => $totalFiles,
                'totalDirs' => $totalDirs,
                'totalSize' => $totalSize,
                'idSelected' => $idSelected
        ];
    }
    public function pasteFiles(Request $req) //incolla i file precedentemente copiati o incollati  
    {   
        $idSelected = $req['idSelected'];
        $function   = $req['function'];
        $total      = $req['total'];
        $totalSize  = $req['totalSize'];
        $totalFiles = $req['totalFiles'];
        $totalDirs  = $req['totalDirs'];

        //copia degli elementi in cima

        return response()->json(['idSelected' => $idSelected,
                                 'function'   => $function,
                                 'total'      => $total
        ],200);
    }
}
