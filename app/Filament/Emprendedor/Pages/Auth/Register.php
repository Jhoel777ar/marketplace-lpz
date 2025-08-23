<?php

namespace App\Filament\Emprendedor\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Filament\Forms\Components\FileUpload;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo institucional')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email')
                            ->rule(function () {
                                return function (string $attribute, $value, $fail) {
                                    $pattern = '/@(edu|ac|umsa\.bo|univ\.bo|\.edu\.bo|upds\.net\.bo)$/i';
                                    if (!preg_match($pattern, $value)) {
                                        $fail('El correo debe ser institucional y pertenecer a un dominio válido (ej: .edu, .ac, umsa.bo, univ.bo, edu.bo o upds.net.bo).');
                                    }
                                };
                            }),
                        Select::make('ubicacion')
                            ->label('En donde se encuentra?')
                            ->required()
                            ->searchable()
                            ->options([
                                'Bolivia' => [
                                    'La Paz - La Paz'   => 'Bolivia / La Paz / La Paz',
                                    'La Paz - El Alto'  => 'Bolivia / La Paz / El Alto',
                                    'Cochabamba'        => 'Bolivia / Cochabamba',
                                    'Santa Cruz'        => 'Bolivia / Santa Cruz',
                                    'Oruro'             => 'Bolivia / Oruro',
                                    'Potosí'            => 'Bolivia / Potosí',
                                    'Tarija'            => 'Bolivia / Tarija',
                                    'Chuquisaca'        => 'Bolivia / Chuquisaca',
                                    'Beni'              => 'Bolivia / Beni',
                                    'Pando'             => 'Bolivia / Pando',
                                ],
                                'Perú' => [
                                    'Lima'              => 'Perú / Lima',
                                    'Cusco'             => 'Perú / Cusco',
                                    'Arequipa'          => 'Perú / Arequipa',
                                ],
                                'Argentina' => [
                                    'Buenos Aires'      => 'Argentina / Buenos Aires',
                                    'Córdoba'           => 'Argentina / Córdoba',
                                    'Santa Fe'          => 'Argentina / Santa Fe',
                                ],
                                'Chile' => [
                                    'Santiago'          => 'Chile / Santiago',
                                    'Valparaíso'        => 'Chile / Valparaíso',
                                ],
                                'México' => [
                                    'CDMX'              => 'México / Ciudad de México',
                                    'Guadalajara'       => 'México / Guadalajara',
                                    'Monterrey'         => 'México / Monterrey',
                                ],
                            ]),
                        TextInput::make('carrera')
                            ->label('Carrera')
                            ->required()
                            ->maxLength(255),
                        Select::make('semestre')
                            ->label('Semestre')
                            ->required()
                            ->options([
                                '1' => '1er Semestre',
                                '2' => '2do Semestre',
                                '3' => '3er Semestre',
                                '4' => '4to Semestre',
                                '5' => '5to Semestre',
                                '6' => '6to Semestre',
                                '7' => '7mo Semestre',
                                '8' => '8vo Semestre',
                                '9' => '9no Semestre',
                                '10' => '10mo Semestre',
                            ]),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        FileUpload::make('carnet_universitario')
                            ->label('Carnet Universitario (opcional se le pedira luego)')
                            ->disk('s3')
                            ->directory('verificacion')
                            ->visibility('public')
                            ->image()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->columnSpan('full')
                            ->required(false),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'carrera' => $data['carrera'],
            'semestre' => $data['semestre'],
            'ubicacion' => $data['ubicacion'],
        ]);
        $user->assignRole('emprendedor');
        if (!empty($data['carnet_universitario'])) {
            $user->verificacionEmprendedor()->create([
                'image_path' => $data['carnet_universitario'],
            ]);
        }
        return $user;
    }
}
