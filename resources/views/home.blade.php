@extends('layouts.app')

@section('content')
<div id="koCompanies">
    
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline">
                
                {{-- <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control" id="nome" placeholder="Cadastrar Empresa">
                </div> --}}
                
                <button type="submit" class="btn btn-default" data-bind="click: newCompany" >Nova Empresa</button>
            </form>
        </div>    
    </div>

    <div class="row" style="margin-top: 30px">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nome</th>
                        <th>Token</th>
                        <th>Fotos</th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: companies">
                    <tr>
                        <!-- ko if: !editFlag() -->
                        <th><i class="fa fa-pencil-square-o" aria-hidden="true"></i></th>
                        <th><span data-bind="text: description"></span></th>
                        <td><span data-bind="text: token"></span></td>
                        <td><span data-bind="text: photos"></span></td>
                        <!-- /ko -->
                        <!-- ko if: editFlag --> 
                        <th><i class="fa fa-pencil-square-o" aria-hidden="true"></i></th>
                        <th><input type="text" class="form-control" data-bind="value: description"></th>
                        <td><span data-bind="text: token"></span></td>
                        <td><span data-bind="text: photos"></span></td>
                        <!-- /ko -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('custom_scripts')

    <script type="text/javascript">        
        
        var data =  {!! $model !!};
        var url_save = "{{ route('company.save') }}";
        var url_delete = "{{ route('company.delete') }}";

        function Company(com_id,com_description,com_token,created_at,photos,editFlag) {

            var self = this;

            self.id          = com_id;
            self.token       = ko.observable(com_token);
            self.editFlag    = ko.observable(editFlag);
            self.photos      = ko.observable(photos);
            self.description = ko.observable(com_description).extend({
                required: { params: true, message: 'O campo Nome é obrigatório.'}
            });

            self.erros = ko.validation.group(self);

            self.delete = function(item) {
                confirmModal.show(
                    'Tem certeza que deseja remover a empresa ?',
                    function() {            

                        var data = { id : item.id()};
                        var deleteCallback = function(response) {
                            if(!response.status) {
                                globalMsgVm.erros([response.mesage]);

                            } else { 
                                viewModel.companies.remove(item);
                                globalMsgVm.showSuccessMessage(response.mesage);
                            }
                        };
                        viewModelComum.doPost(url_delete, data, deleteCallback);
                    }
                );
            };
            
            self.save = function() {
                
                if (self.erros().length > 0) {
                    globalMsgVm.erros(self.erros());
                    return;
                }
                
                var data = {
                    id:    self.id(),
                    description:  self.description()
                };

                var saveCallback = function(response) {
                    if(!response.status) {
                        globalMsgVm.erros([response.message]);
                        return;

                    } else  {
                        self.id(response.company.id);
                        self.token(response.company.token);
                        self.editFlag(false);
                        globalMsgVm.showSuccessMessage(response.message);
                    }
                };
                viewModelComum.doPost(url_save, data, saveCallback);
                
            };
            
            self.original = null;
            self.edit = function() {
                self.original = {description:description};
                self.editFlag(true);
            };
            
            self.cancelar = function() {
                if (!self.id()) {
                    viewModel.companies.remove(self);
                    return;
                }
                self.goBackData();
                self.editFlag(false);
            };
            
            self.goBackData = function() {
                self.description(self.original.description);
            };
        }

        
        function ViewModel() {
            var self = this;
            
            self.companies = ko.observableArray();
            
            self.setData = function(companies) {

                self.companies(ko.utils.arrayMap(companies, function(item) {
                    return new Company(
                        item.com_id,
                        item.com_description,
                        item.com_token,
                        item.created_at,
                        item.photos,
                        false
                    );
                }));
            };

            self.newCompany = function() {
                self.companies.push(new Company(
                    null,
                    null,
                    null,
                    null,
                    0,
                    true
                ));
            };
        }
    
        var viewModel;
        $(document).ready(function () {
            viewModel = new ViewModel();
            viewModel.setData(data);
            ko.applyBindings(viewModel, document.getElementById('koCompanies'));
            
        });
        
    </script>

@endsection
