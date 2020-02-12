<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cloud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_owner');//proprietario del record descrittivo
            $table->string('tipo_record');/*tipo record :   FILE(val1=percorso)(val2=padre)(val3=users)(val4=url)(val5=dimensione del file)
                                                            CARTELLA(val1=nome cartella)(val2=padre)(val3=users)
                                                            MAX_GB(val1=2GB di default)
                                                            MAX_CARTELLE(val1=100 di default)
                                                            MAX_FILE_X_CART(val1=200 di default)
                                                            */
            $table->string('val1');
            $table->string('val2')->nullable();
            $table->string('val3')->nullable();//se questo campo viene usato per la condivisione si deve creare un record per ogni utente o gruppo che è ammesso alla condivisione
            $table->string('val4')->nullable();
            $table->integer('val5')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
