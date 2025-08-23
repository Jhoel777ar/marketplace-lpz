<?php

namespace App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\EmprendedorVerificacion;

class CreateEmprendedorVerificacion extends CreateRecord
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return EmprendedorVerificacion::create([
            'user_id' => auth()->id(),
            ...$data,
        ]);
    }
}
