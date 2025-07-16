@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4>Importação de Dados</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Importar Clientes</h5>
                        <p class="text-muted">Faça upload de um arquivo CSV, XLS ou XLSX com os dados dos clientes.</p>
                        
                        <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Arquivo de Clientes</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                       id="file" name="file" accept=".csv,.xls,.xlsx" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Formatos aceitos: CSV, XLS, XLSX (máximo 10MB)
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Importar Clientes
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Template de Importação</h5>
                        <p class="text-muted">Baixe o template com o formato correto para importação.</p>
                        
                        <a href="{{ route('import.template') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-download"></i> Baixar Template CSV
                        </a>
                        
                        <div class="mt-3">
                            <h6>Campos obrigatórios:</h6>
                            <ul class="small">
                                <li><strong>nome:</strong> Nome completo do cliente</li>
                                <li><strong>cpf:</strong> CPF do cliente (apenas números)</li>
                                <li><strong>e_mail:</strong> Email válido do cliente</li>
                            </ul>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Campos opcionais:</h6>
                            <ul class="small">
                                <li>data_nasc, especialidade, cidade_atendimento</li>
                                <li>uf, regiao, instagram, telefone</li>
                                <li>vendedor, turma, status, classificacao</li>
                                <li>medboss, crmb, datas de início/término</li>
                                <li>valores, observações, etc.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-12">
                        <h5>Instruções de Importação</h5>
                        <div class="alert alert-info">
                            <h6>Como usar:</h6>
                            <ol>
                                <li>Baixe o template CSV clicando no botão acima</li>
                                <li>Preencha os dados dos clientes seguindo o formato do exemplo</li>
                                <li>Salve o arquivo em formato CSV, XLS ou XLSX</li>
                                <li>Faça o upload do arquivo usando o formulário</li>
                                <li>O sistema criará automaticamente clientes, vendedores e inscrições</li>
                            </ol>
                            
                            <h6>Observações importantes:</h6>
                            <ul>
                                <li>CPFs duplicados serão ignorados (não sobrescreverão dados existentes)</li>
                                <li>Datas devem estar no formato AAAA-MM-DD ou DD/MM/AAAA</li>
                                <li>Valores monetários podem usar vírgula ou ponto como separador decimal</li>
                                <li>O campo "ativo" aceita SIM/NÃO ou 1/0</li>
                                <li>O campo "medboss" aceita SIM/NÃO</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

