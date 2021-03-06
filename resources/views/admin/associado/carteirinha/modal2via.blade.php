


<div class="modal fade" id="2via" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Insira abaixo o motivo do pedido de 2ª via:</h4>
         </div>
         <div class="modal-body">
                  <div class="modal-body">
            <form class="form-horizontal" method="POST" action="{{ url('/associado/carteirinha/2via') }}">
               {{ csrf_field() }}
               <fieldset>

                 <div class="form-group">
                     <label class="col-md-4 control-label" for="digito">Pedido de 2ª via:</label>
                     <div class="col-md-4">
                        <textarea  id="observacao" name="observacao" class="form-control input-md"></textarea>
                        <p> O preenchimento de observações não é obrigatório. </p>
                        <p> Logo validaremos o seu pedido. </p>
                     </div>
                  </div>
                <input value="{{ Auth::user()->id }}" name="id" type="hidden"/>
               </fieldset>
               <div class="modal-footer">
                  <button class="btn btn-success btn-block" type="submit">
                  OK
                  </button>
               </div>
            </form>
         </div>

      </div>
   </div>
