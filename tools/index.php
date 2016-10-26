<?php


require_once "../libs/proj/inc.php";
require_once "../inc/common.inc.php";

insert_header();
?>

<style type="text/css">
.code-block{
    height: 99%;
    width: 100%;
    border: 1px solid #ddd;
    padding: 10px;
    font-family: monospace;
    font-size: 10px;
    resize: none;
}
</style>

<script type="text/javascript">
    $(function(){

        function hex2a(hexx) {
            var hex = hexx.toString();//force conversion
            var str = '';
            for (var i = 0; i < hex.length; i += 2)
                str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
            return str;
        }

        String.prototype.hexEncode = function(){
            var hex, i;

            var result = "";
            for (i=0; i<this.length; i++) {
                hex = this.charCodeAt(i).toString(16);
                result += ("000"+hex).slice(-4);
            }

            return result
        }

        String.prototype.hexDecode = function(){
            var j;
            var hexes = this.match(/.{1,4}/g) || [];
            var back = "";
            for(j = 0; j<hexes.length; j++) {
                back += String.fromCharCode(parseInt(hexes[j], 16));
            }

            return back;
        }

        function fromHex(hex,str){
            try{
                str = decodeURIComponent(hex.replace(/(..)/g,'%$1'))
            }
            catch(e){
                str = hex
                console.log('invalid hex input: ' + hex)
            }
            return str
        }

        function toHex(str,hex){
            try{
                hex = unescape(encodeURIComponent(str))
                    .split('').map(function(v){
                        return v.charCodeAt(0).toString(16)
                    }).join('')
            }
            catch(e){
                hex = str
                console.log('invalid text input: ' + str)
            }
            return hex
        }


        $('.convert-to-js').on('click', function(){
            var val = JSON.stringify(jQuery.parseJSON(fromHex($('.code-block-hex').eq(0).val())), null, 4);
            $('.code-block-js').eq(0).val(val)
        })

        $('.convert-to-hex').on('click', function(){
            $('.code-block-hex').eq(0).val(toHex($('.code-block-js').eq(0).val()))
        })


    })
</script>


    <div class="row">

        <div class="col-md-6">
            <div class="panel panel-primary">

                <div class="panel-heading panel-heading-flex">

                    <div>HEX</div>

                    <button type="button" class="btn btn-default btn-xs convert-to-js">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="panel-body tab-groups-fullscreen">
                    <textarea class="code-block code-block-hex" contenteditable="true"></textarea>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading panel-heading-flex">

                    <button type="button" class="btn btn-default btn-xs convert-to-hex">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    </button>

                    <div>JS</div>
                </div>

                <div class="panel-body tab-groups-fullscreen">
                    <textarea class="code-block code-block-js" contenteditable="true"></textarea>
                </div>
            </div>
        </div>

    </div>

<?

insert_footer();
