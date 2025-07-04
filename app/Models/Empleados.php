<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OpenApi\Annotations as OA;
use Carbon\Carbon;
use App\EstadoEmpleado;

/**
 * @OA\Schema(
 *     schema="Empleado",
 *     title="Empleado",
 *     description="Modelo del empleado",
 *     type="object",
 *     required={"id_persona", "id_cargo", "id_departamento", "fecha_ingreso", "salario_base", "estado_empleado"},
 *     @OA\Property(property="id_empleados", type="integer", readOnly=true),
 *     @OA\Property(property="id_persona", type="integer", example=1),
 *     @OA\Property(property="id_cargo", type="integer", example=2),
 *     @OA\Property(property="id_departamento", type="integer", example=3),
 *     @OA\Property(property="fecha_ingreso", type="string", format="date", example="2023-05-10"),
 *     @OA\Property(property="salario_base", type="number", format="float", example=2500.75),
 *     @OA\Property(property="estado_empleado", type="string", example="Activo"),
 *     @OA\Property(property="fecha_egreso", type="string", format="date", nullable=true, example="2024-12-31")
 * )
 */


final class Empleados extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';

    protected $fillable = [
        'id_persona',
        'id_cargo',
        'id_departamento',
        'fecha_ingreso',
        'salario_base',
        'estado_empleado',
        'fecha_egreso',
    ];

    protected $dates = [
        'fecha_ingreso',
        'fecha_egreso',
    ];

    protected $casts = [
        'salario_base' => 'float',
        'fecha_ingreso' => 'date:Y-m-d',
        'fecha_egreso' => 'date:Y-m-d',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'id_persona');
    }

    protected $appends = ['cantidad_hijos_menores_18',
                            'nombre_persona',
                            'nombre_cargo',
                            'nombre_departamento',];

    public function getCantidadHijosMenores18Attribute(): int
    {
        $this->loadMissing('persona.hijos');
        return $this->persona?->hijos
            ->filter(function ($hijo) {
                return $hijo->discapacitado || Carbon::parse($hijo->fecha_nacimiento)->age < 18;
            })->count() ?? 0;
    }

    public function nominaDetalleCuotas()
    {
        return $this->hasMany(NominaDetalleCuota::class, 'id_empleado', 'id_empleado');
    }

    public function getNombrePersonaAttribute(): ?string
    {
        // Carga la relación si no está cargada
        if (!$this->relationLoaded('persona')) {
            $this->load('persona');
        }

        // Retorna el nombre completo si la persona está relacionada
        return $this->persona?->nombre_completo;
    }

    public function getNombreCargoAttribute(): ?string
    {
        if (!$this->relationLoaded('cargo')) {
            $this->load('cargo');
        }

        return $this->cargo?->nombre_cargo;
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id');
    }

    public function getNombreDepartamentoAttribute(): ?string
    {
        if (!$this->relationLoaded('departamento')) {
            $this->load('departamento');
        }

        return $this->departamento?->nombre_departamento;
    }

    public function nominasAdicionalesFijas()
    {
        return $this->hasMany(NominaAdicionalFijo::class, 'id_nomina', 'id_empleado')->get();
    }

    public function nominasPorcentualesMinimas()
    {
        return $this->hasMany(NominaPorcentualMinimo::class, 'id_nomina', 'id_empleado');
    }

    public function nominaPorcentajeSalarioBase()
    {
        return $this->hasMany(NominaPorcentajeSalarioBase::class, 'id_nomina', 'id_empleado')->get();
    }

    public function getNominaPorcentajeSalarioBaseAttribute()
    {
        return $this->nominaPorcentajeSalarioBase();
    }

    public function getNominaFijoSalarioAttribute()
    {
        return $this->nominasAdicionalesFijas();
    }

    public function getPersonaCompleta()
    {
        // Asegúrate de que la relación 'persona' esté cargada
        if (!$this->relationLoaded('persona')) {
            $this->load('persona'); // Cargar la relación si no está cargada
        }

        // Retorna la relación persona, que es el modelo completo
        return $this->persona; 
    }

    public function scopeActivos($query)
    {
        return $query->where('estado_empleado', '!=', EstadoEmpleado::Inactivo->value);
    }

    public function detalleNomina()
    {
        return $this->hasMany(DetalleNomina::class, 'id_empleado', 'id_empleado');
    }
}