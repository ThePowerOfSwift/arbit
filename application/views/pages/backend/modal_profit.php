<div class="modal fade" id="profitModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <p class="text-center">
        <div style="font-size:12px">
			<div class="col-md-12">
			    <center><p class="center section-subtitle">aBOT PROFIT CALCULATOR</p>					
			    Daily profit amounts are not guaranteed and are only paid when aBOT generates a profit from Arbitrage trading. However it is possible for aBOT to generate an average of 30% a month.
			    <br>
			    <br>
			    <br>
			    <input type=number id="dollars" onkeyup="calc()"  style="color:black;"/> <br>
			    <p>Enter $ Amount:</p>
			    <br>
                <label> Selected Days: <span id="days"></span></label><input type="range" min="1" max="90" onchange="calc()" value="50" class="slider" id="myRange">
                <p> Select Days (1 - 90) </p></center>
                <br>
                 <center>
			    <input type=number id="total" value="" readonly style="color:black;"> <br>
                <p>Total Estimated Profit (USD):</p>
                (No profits from bot are guaranteed and are only realized when bot generates trades with profit from arbitrage market opportunities.)
			<br><br>
			</div>
			
			<script>
			function calc(){
			    $('#days').html($('#myRange').val());
			    $('#total').val(($('#myRange').val() / 102) * $('#dollars').val());
			
			}
			
			</script>                    
       </tbody> 
                </table>
            </div>
            <br/>
            </div>
        </div>
        </div>
        </div>
    </div>
    </div>
</div>