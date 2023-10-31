<?php

use App\Core\Controller;

class Clientes extends Controller
{
  public function index()
  {
    $clienteModel = $this->model("Cliente");

    $registros = $clienteModel->listarClientes();

    echo json_encode($registros, JSON_UNESCAPED_UNICODE);
  }

  public function store()
  {
    $novoCliente = $this->getRequestBody();

    $clienteModel = $this->model("Cliente");
    $clienteModel->nomeCliente = $novoCliente->nome_cliente;
    $clienteModel->placaCarro = $novoCliente->placa_carro;
    $clienteModel->dataHoraEntrada = $novoCliente->data_hora_entrada;

    $precoModel = $this->model("Preco");
    $ultimoPreco = $precoModel->getUltimoInserido();

    $clienteModel->precoId = $ultimoPreco->id;

    $clienteModel = $clienteModel->inserir();

    if ($clienteModel) {
      http_response_code(201);

      echo json_encode($clienteModel);
    } else {
      http_response_code(500);

      echo json_encode(["erro" => "Problemas ao inserir preço."]);
    }
  }

  public function delete($id)
  {
    $clienteModel = $this->model("Cliente");

    $clienteModel = $clienteModel->buscarPorId($id);

    if (!$clienteModel) {
      http_response_code(404);

      echo json_encode(["erro" => "Registro não encontrado."]);

      exit;
    }

    $clienteModel = $this->calcularValor($clienteModel);

    $clienteModel->atualizar();

    echo json_encode($clienteModel, JSON_UNESCAPED_UNICODE);
  }

  private function calcularValor($clienteModel)
  {
    $dataEntrada = DateTime::createFromFormat("Y-m-d H:i:s", $clienteModel->dataHoraEntrada);

    $dataSaida = new DateTime();

    $intervalo = $dataSaida->diff($dataEntrada);

    $horas = 0;

    if ($intervalo->d > 0) {
      $horas = $horas + $intervalo->d * 24;
    }

    $horas = $horas + $intervalo->h;

    if ($intervalo->i > 10) {
      $horas += 1;
    }

    $precoModel = $this->model("Preco");

    $precoModel = $precoModel->buscarPorId($clienteModel->precoId);

    $clienteModel->valorTotal = $precoModel->primeiraHora;

    $horas--;

    if ($horas > 0) {
      $clienteModel->valorTotal += $precoModel->demaisHoras * $horas;
    }

    $clienteModel->dataHoraSaida = $dataSaida->format("Y-m-d H:i:s");

    return $clienteModel;
  }
}