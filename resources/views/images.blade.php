@extends('layouts.app')

@section('content')
<div id="koImages">

    <div class="row">
    
        <div class="col-md-12">
            <h1 data-bind="text: companyName"></h1>
        </div>
        
        <div class="col-md-12">
            <div class="form-group mt30">
                <button type="button" class="btn btn-primary" data-bind="click: toggleImages, text: textToggleImages"></button>
                <label class="btn btn-info btn-file">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Carregar Imagens <input type="file" hidden data-bind="event: {'change': function() { fileSelected($element); }}" multiple>
                </label>
            </div>
        </div>    
    </div>
    <div class="row mt30">
        <div class="col-md-12">
            <!-- ko if: modTable -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="min-width: 50px"></th>
                        <th>Código</th>
                        <th>Path</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: images">
                    <tr>
                        <td class="center">
                            <i class="fa fa-trash-o cursor-pointer" aria-hidden="true" data-bind="click: remove"></i>
                        </td>
                        <td><span data-bind="text: id"></span></td>
                        <td><span data-bind="text: path"></span></td>
                        <td><span data-bind="text: created"></span></td>
                    </tr>
                </tbody>
            </table>
            <!-- /ko -->
            <!-- ko if: !modTable() -->
                <div class="row">
                <!-- ko foreach: images -->
                    <div class="col-md-3">
                        <ul class="thumbnails" style="padding-left: 0">
                            <li class="span4 thumbnail">
                                <a href="http://www.flinders.edu.au/">
                                <img data-bind="attr: {src: domainPath+path}" width="300" height="200" class="image-list"></a>
                                <div class="caption">
                                <p data-bind="text: id + ' - ' + created"></p>
                                <p>
                                    <a href="#" class="btn btn-primary" role="button">
                                        <i class="fa fa-trash-o cursor-pointer" aria-hidden="true" data-bind="click: remove"></i>
                                    </a>
                                </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                <!-- /ko -->
                </div>
            <!-- /ko -->
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')

    <script type="text/javascript">
        
        var data =  {!! json_encode($model) !!};
        var url_save = "{{ route('image.save') }}";
        var url_delete = "{{ route('image.delete') }}";
        
        function Image(img_id, img_path, created_at) {
            var self = this;
            
            self.id = img_id;
            self.path = img_path;
            self.created = created_at;
            
            self.remove = function(item) {

                confirmModal.show(
                    'Tem certeza que deseja remover a imagem ?',
                    function() {            

                        var data = {
                            id : item.id,
                            _token: '{{ csrf_token() }}',
                        };
                        var deleteCallback = function(response) {
                            if(!response.status) {
                                globalMsgVm.erros([response.message]);

                            } else { 
                                viewModel.images.remove(item);
                                globalMsgVm.showSuccessMessage(response.message);
                            }
                        };
                        viewModelComum.doPost(url_delete, data, deleteCallback);
                    }
                );
            };
        }
        
        function ViewModel() {
            var self = this;
            
            self.images = ko.observableArray();
            self.companyId = null;
            self.companyName = null;
            self.modTable = ko.observable(true);
            self.showModImages = ko.observable(false);
            self.textToggleImages = ko.observable();
            
            self.setData = function(model) {

                self.images(ko.utils.arrayMap(model.images, function(item, i) {
                    return new Image(item.img_id, item.img_path, item.created_at);
                }));
                self.companyId = model.company.com_id;
                self.companyName = model.company.com_description;

            }
            
            self.fileSelected = function(el) {
                
                if (el) {
                    
                    var counter = -1,
                        file,
                        formData = new FormData(),
                        imageCounter = 0;
                    while ( file = el.files[ ++counter ] ) {
                        
                        if(file.size > 10 * 1024 * 1024) {
                            globalMsgVm.erros(['Arquivo grande demais.']);

                        } else {
                            var fileNamePieces = file.name.split('.');
                            var extension = fileNamePieces[fileNamePieces.length - 1];

                            if (extension != 'jpg' && extension != 'png' && extension != 'jpeg') {
                                globalMsgVm.erros(['Tipo de arquivo inválido.']);
                                return;
                            }
                            
                            formData.append('images['+imageCounter+']', file);
                            imageCounter++;
                        }
                    }
                }                    

                self.sendFile(formData);
            }
            
            self.sendFile = function(formData) {
                
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('companyId', self.companyId);

                var saveCallback = function(response) {

                    if(!response.status) {
                        globalMsgVm.erros([response.message]);
                        return;

                    } else {

                        self.setData(response.data);
                        globalMsgVm.showSuccessMessage([response.message]);
                    }
                };

                viewModelComum.doPostImage(url_save, formData, saveCallback);
            };

            self.toggleImages = function() {
                self.showModImages(!self.showModImages());
                self.modTable(!self.modTable());
            };
            self.computedImages = ko.computed(function() {
                if (self.showModImages()) {
                    self.textToggleImages('Exibir Modo Tabela');
                
                } else {
                    self.textToggleImages('Exibir Modo Imagens');
                }
            });

        }
    
        var viewModel;
        $(document).ready(function () {
            viewModel = new ViewModel();
            viewModel.setData(data);
            ko.applyBindings(viewModel, document.getElementById('koImages'));
            
        });
        
    </script>

@endsection
