<?php

namespace App\Livewire\Salario;

use Livewire\Component;
use App\Services\NominaService;
use App\EstadoNomina;

final class ProcesarIndex extends Component
{
    public $showForm;
    public $verificarPlanilla;
    public $planilla_procesada;
    public $lista_planilla = [];
    public $id_nomina;
    
    private $nominaService;

    protected $listeners = ['planillaCreate' => 'closeForm'];

    public function confirma($id)
    {
        $this->nominaService = new NominaService();
        $this->nominaService->confirmaPlanilla($id);
        $this->recargaVista();
    }

    public function visualizar($id)
    {
        $this->id_nomina = $id;
        $this->verificarPlanilla = true;
    }
    
    public function delete($id)
    {
        $this->nominaService = new NominaService();
        $this->nominaService->borrarPlanillaPorId($id);
        $this->recargaVista();
    }

    private function recargaVista()
    {
        $this->nominaService = new NominaService();
        $this->fill_data();
    }
    
    public function create()
    {
        $this->showForm = true;
    }

    public function mount()
    {
        $this->recargaVista();
        $this->planilla_procesada = EstadoNomina::Confirmado->value;
    }

    private function fill_data()
    {
        $this->showForm = false;
        $this->verificarPlanilla = false;
        $this->lista_planilla = $this->nominaService->obtenerListadoNominas();
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->verificarPlanilla = false;
        $this->recargaVista();
    }

    public function render()
    {
        return view('livewire.salario.procesar-index')->layout('layouts.app');
    }
}
