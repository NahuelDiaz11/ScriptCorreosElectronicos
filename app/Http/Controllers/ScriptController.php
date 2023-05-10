<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScriptController extends Controller
{
    public function script()
    {
        $correos = DB::table('prueba_winpax')->where('status', 1)->get();

        $correo_acu = array();
        //definir variable int i
        $i = 0;
        foreach ($correos as $key => $correo) {
            foreach ($correo as $key => $value) {
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $i++;
                    $correo_acu[$i] = $value;
                }
            }
        }

        $correos_json = DB::table('prueba_winpax')->where('status', 1)->pluck('json_1');

        // convierte json en array
        $correos_json = $correos_json->map(function ($item) {
            return json_decode($item, true);
        });


        $correos_json = $correos_json->flatMap(function ($item) {
            if (isset($item['email'])) {
                return [$item['email']];
            } else {
                return [];
            }
        });

        // combina los dos arrays de correos
        $correos = array_merge($correo_acu, $correos_json->toArray());

        // convierte el array de correos en una cadena
        $correos_str = implode("\n", $correos);

        // crea archivo llamado "correos.txt" con los correos
        file_put_contents('correos.txt', $correos_str);

        return $correos;
    }

}