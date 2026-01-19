<div class="tab-pane fade show" id="pollmessage" role="tabpanel">
	<form class="row g-3" action="{{ route('messagetest') }}" method="POST">
		@csrf
		<div class="col-12">
			<label class="form-label">{{__('Sender')}}</label>
			<input name="sender" value="{{ session()->get('selectedDevice')['device_body'] ?? '' }}" type="text" class="form-control" readonly>
		</div>
		<div class="col-12">
			<label class="form-label">{{__('Receiver Number')}} *</label>
			<textarea placeholder="628xxx|628xxx|628xxx" class="form-control" name="number" id="" cols="20" rows="2" required></textarea>
		</div>
		<input type="hidden" name="type" value="poll" />
		<label for="name" class="form-label">{{__('Name / Question')}}</label>
		<textarea type="text" name="name" class="form-control" id="name" required></textarea>
		<label for="countable" class="form-label">{{__('Only one answer per number')}}</label>
		<select class="default-select form-control wide" id="countable" name="countable">
			<option selected value="1">{{__('Yes')}}</option>
			<option value="0">{{__('No')}}</option>
		</select>
		<div class="poll-area"></div>

		<div class="d-flex flex-wrap gap-2 mt-4">
			<button type="button" id="addoption" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
				<i class="ti tabler-plus me-1"></i>{{__('Option')}}
			</button>
			<button type="button" id="reduceoption" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center">
				<i class="ti tabler-minus me-1"></i>{{__('Option')}}
			</button>
		</div>

		<div class="col-12 text-center">
			<button type="submit" class="btn btn-outline-primary btn-sm px-5">{{__('Send Message')}}</button>
		</div>
	</form>
</div>

<script>
	window.addEventListener('load', function() {
	    $(document).ready(function() {
	      
	        var max_fields = 5;
	        var wrapper = $(".poll-area");
	        var add_button = $("#addoption");
	        var x = 0;
	        $(add_button).click(function(e) {
	            e.preventDefault();
	            if (x < max_fields) {
	                x++;
	
	                $(wrapper).append('<div class="form-group optioninput"><label for="option[' + x + ']" class="form-label">{{__("Option")}} ' + x + '</label><input type="text" name="option[' + x + ']" class="form-control" id="option[' + x + ']" required></div>'); //add input box
	            } else {
	                toastr['warning']('{{__("Maximal 5 option")}}');
	            }
	        });
	        $(document).on('click', '#reduceoption', function(e) {
	            e.preventDefault();
	           if(x > 0){
	
	            $('.optioninput').last().remove();
	            x--;
	           }
	        });
	       
	    });
	});
	
</script>