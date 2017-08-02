
<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<!--jquery min-->
<script src="{{asset('js/jquery-ui-1.10.4.custom.min.js')}}"></script>

<!--knockout-->
<script src="{{asset('js/knockout-3.4.0.js')}}"></script>
<!--knockout validation-->
<script src="{{asset('js/knockout.validation.min.js')}}"></script>

<script>

    function LoadingModalViewModel(){
        var self = this;
        self.showLoading = ko.observable();
    }

    function GlobalMsgs () {
        var self = this;
        self.erros = ko.observableArray();
        self.showSuccessMessage = function(message) {
            $("#successDiv > p").html(message);
            $("#successDiv").fadeIn('fast');
            setTimeout(function() {
                $("#successDiv").fadeOut(300);
            }, 3000);
            
            $("html, body").animate({ scrollTop: 0 }, "slow");
        }
        
        self.top = ko.computed(function() {
           if (self.erros().length > 0) {
               $("html, body").animate({ scrollTop: 0 }, "slow");
           } 
        });
    }

    function ConfirmModal(){
        var self = this;
        self.confirmTitle = ko.observable();
        
        self.okButton           = null;
        self.cancelarButton     = null;
        self.textoBotaolOk       = ko.observable();
        self.textoBotaolCancelar = ko.observable();

        /* 
            funcao responsavel por renderizar tela de confirmacao. nenhum parametro é obrigatório, para mudar
            o comportamento basta passar a string da mensagem na primeira posição, a função que será executada com o click do botão ok/sim,
            um booleano para definir se o botão é sim ou ok se não informado o default é ok/cancelar se true sim/não
            e uma função para ação do botão cancelar/não não obrigatoria.
            ex
                confirmModal.show(
                    //texto exibido em tela
                    'teste de tela',
                    //ação do botão sim
                    function()
                    {
                        //executar logica do botão sim
                        console.log('ação do botão sim');
                    },
                    // booleano para trocar o texto de ok para sim
                    true
                );
        */
        self.show = function (stringMessage, callbackOk, callbackCancelar)
        {
            self.confirmTitle(stringMessage);
            
            self.okButton = callbackOk != undefined 
            ?   function () 
                {
                    callbackOk();
                    $('#confirmModal').modal('hide');
                } 
            :  function()
            {
                $('#confirmModal').modal('hide');
            }
    
            self.cancelarButton = callbackCancelar != undefined 
            ? function () 
            {
                callbackCancelar();
                $('#confirmModal').modal('hide');
            } 
            :  function()
            {
                $('#confirmModal').modal('hide');
            }
            
            $('#confirmModal').modal('show');
        }
    }
    
    function ViewModelComum() {
        var self = this;
        
        // executar chamada ajax do tipo post
        self.doPost = function(url, data, callback) {
            
            modalViewM.showLoading(true);
            $.post( url, data, function(data) {
                try {
                    callback(data);
                } catch(exception){
                    callback(ko.toJSON({status: 0, mensagem: 'Ocorreu um erro no retorno do serviço.'}));
                }
            })
            .fail(function($err) {
                if ($err.status == 422) {
                    globalMsgVm.erros($err.responseJSON.description);
                    return;
                }
                globalMsgVm.erros(['Ocorreu um erro na execução do serviço.']);
            })
            .always(function() {
                modalViewM.showLoading(false);
            });
        };
        
        // executar chamada ajax do tipo get
        self.doGet = function(url, callback) {
            $.get(url, callback)
            .fail(function(){
                globalMsgVm.erros(['Ocorreu um erro na execução do serviço.']);
            })
            .always(function() {
            });
        };
        
        // executar chamada ajax do tipo post
        self.doPostImage = function(url, formData, callback) {
            
            modalViewM.showLoading(true);
            $.ajax({
                url: url,
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function(data) {
                    try {
                        callback(data);
                    } catch(exception){
                        callback(ko.toJSON({status: 0, mensagem: 'Ocorreu um erro no retorno do serviço.'}));
                    }
                },
                error: function(data) {
                    globalMsgVm.erros(['Ocorreu um erro na execução do serviço.']);
                },
                complete: function(data) {
                    modalViewM.showLoading(false);
                }
            });
        };
    }

    var domainPath = document.location.origin+"/";
    var publicPath = domainPath+"public";
    var viewModelComum = new ViewModelComum();
    var modalViewM = new LoadingModalViewModel();
    var globalMsgVm = new GlobalMsgs();
    var confirmModal = new ConfirmModal();  

    $(document).ready(function(){
        
        ko.applyBindings(modalViewM,document.getElementById('koModal'));
        ko.applyBindings(globalMsgVm,document.getElementById('koGlobalMsgs'));
        ko.applyBindings(confirmModal,document.getElementById('confirmModal'));
    });

</script>