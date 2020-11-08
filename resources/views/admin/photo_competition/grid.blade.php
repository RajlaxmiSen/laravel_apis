<div>
    <div class="abl-pager">
        <span class="pull-left">Showing {{$pager_data['start']}} to {{$pager_data['end']}} of {{$pager_data['global_count']}}
        </span>
        <div class="pull-right" style="margin-right: 10px">{{ $photos->links()}}
        </div>
</div>
<table class="table table-striped table-bordered table-hover table-checkable order-column" id="grid">
    <thead>
        <tr>
            <th width="5%"> S.NO </th>
            <th width="10%"> Comp. No. </th>
            <th width="15%"> Photo </th>
            <th width="10%"> User </th>
            <th width="10%"> Votes</th>
            <th width="10%"> State</th>
            <th width="5%"> Country </th>
            <th width="5%"> Winner </th>
            <th  width="10%" class="nosort"> Actions </th>
        </tr>
    </thead>

<?php
$sno=$pager_data['start'];
$i=0;
?>
    <tbody id="tax-search-filter">
        <tr class="abl-filter-row">
            <td class="text-center">
               {{--  {!! Form::text("search_id",isset($sr_dt['id'])?$sr_dt['id']:"",["class"=>"form-control abl-filter ","placeholder"=>"ID","style"=>"width:50%;margin-left:25%"]) !!} --}}
            </td>
            <td class="text-center">
                {!! Form::text("Comp Id",isset($sr_dt['comp_id'])?$sr_dt['comp_id']:"",["class"=>"form-control abl-filter","placeholder"=>"Comp Id "]) !!}
            </td>
            <td class="text-center"></td>
            <td></td>
            <td></td>
            <td>
               {{--  {!! Form::text("Comp Id",isset($sr_dt['comp_id'])?$sr_dt['comp_id']:"",["class"=>"form-control abl-filter","placeholder"=>"Comp Id "]) !!} --}}
            </td>
            <td>
                
            </td>
            <td>
                <select class="form-control select2 abl-filter" name="is_winner">
                    <option value="">Select Is winner</option>
                    <option value="0" {{(isset($sr_dt['is_winner']) && $sr_dt['is_winner']==0)?"selected":""}} >No</option>  
                    <option value="1" {{(isset($sr_dt['is_winner']) && $sr_dt['is_winner']==1)?"selected":""}} >Yes</option>
                </select>  
            </td>
            <td class="text-center">
                {!! Form::button("<i class='fa fa-filter'></i>",["class"=>"form-control btn btn-primary text-center pull-left" ,"onclick"=>"searchfeeds();","style"=>'width:50px;',"title"=>"Filter"]) !!}
                {!! Form::button("<i class='fa fa-refresh'></i>",["class"=>"form-control btn btn-default pull-right" ,"onclick"=>"resetfeeds();","style"=>'width:50px;',"title"=>"Remove Filters"]) !!}
            </td>
        </tr>
    </tbody>

    <tbody>

        @if(count($photos))                                       
        @foreach($photos as $photo)
        <tr class="odd gradeX">
            <td>{{$sno++}} </td>
            <td>{{$photo->comp_id}}</td>
            <td>{{$photo->photo_path}}</td>
            <td>{{$photo->user->name}}</td>
            <td>{{$photo->votes_count}}</td>
            <td>{{$photo->state}}</td>
            <td>{{$photo->country}}</td>
            <td>{{$photo->is_winner}}</td>
            <td style="width:15%;text-align:center;    vertical-align: middle;">                        
            {!! Form::close() !!}
            </td>
        </tr>
    @endforeach                                                    
    @endif
    </tbody>

</table>

<div class="abl-pager">
    <span class="pull-left">Showing {{$pager_data['start']}} to {{$pager_data['end']}} of {{$pager_data['global_count']}}
    </span>
    <div class="pull-right" style="margin-right: 10px">{{ $photos->links()}}</div>
</div>
<div class="clearfix"></div>
</div>

{{-- User Modal --}}
<div class="modal fade" id="userInfoModel" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">User Info Details</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

{{-- Feed Modal --}}
<div class="modal fade" id="feedInfoModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Feed Info Details</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    $('document').ready(function(){
        // $('select').select2();
        // jQuery('#attendanceAddModal #add_date,.abl-date').datepicker({
        //     format: 'yyyy-mm-dd',
        //     endDate: '+0d',
        //     autoclose: true
        // });

        // jQuery('#d_to').datepicker({
        //     format: 'yyyy-mm-dd',
        //     endDate: '+0d',
        //     autoclose: true
        // });

        // jQuery('#d_from').datepicker({
        //     format: 'yyyy-mm-dd',
        //     endDate: '+0d',
        //     autoclose: true
        // });
        // var timein_clock=$('.abl-clock').clockpicker({donetext:'Done',placement:'right',autoclose:true,afterDone:function(){
        //     console.log('tiemma',timein_clock);
        //  }});
    });

    function  showFeedDetails(feed_id){
        $.ajax({
            type: 'GET',
            url: "{{ URL::to('admin/feeds/showFeedInfo') }}",
            data: "feed_id="+feed_id,
            dataType: 'html',
            success: function (response) {
                console.log(response);
                $('#feedInfoModal div.modal-body').html(response);
                $('#feedInfoModal').modal('show');
            },
            error: function (data) {
                    // Error...
                    var errors = data.responseJSON;
                    var error_string="";
                    $.each(errors, function (index, value) {
                        error_string+=value;
                    });
                    toastr.error(error_string);
                }
            });

    } 

    function  showUserDetails(user_id){
        $.ajax({
            type: 'GET',
            url: "{{ URL::to('admin/feeds/showUserInfo') }}",
            data: "user_id="+user_id,
            dataType: 'html',
            success: function (response) {
                console.log(response);
                $('#userInfoModel div.modal-body').html(response);
                $('#userInfoModel').modal('show');
            },
            error: function (data) {
                    // Error...
                    var errors = data.responseJSON;
                    var error_string="";
                    $.each(errors, function (index, value) {
                        error_string+=value;
                    });
                    toastr.error(error_string);
                }
            });

    }  

    function approve(element){   
       var id = $(element).attr('data-feed-id');
        bootbox.confirm("Are you sure to approved feed!", function(result){ 
            if(result){
                $.ajax({
                       type: 'GET',
                        url: "{{ URL::to('admin/feeds/approve') }}",
                        data:{id:id},
                        dataType: 'json',
                        success: function(data) {
                            toastr.success(data.message);
                                // $('#'+id).html('Approved');
                                resetfeeds();
                        },
                        error: function(e) {
                        console.log(e.message);
                        }
                    });
                }
            });     
        return ;

    }

    function disApprove(element){   
       var id = $(element).attr('data-feed-id');
        bootbox.confirm("Are you sure to disapproved feed!", function(result){ 
            if(result){
                $.ajax({
                    type: 'GET',
                    url: "{{ URL::to('admin/feeds/disapprove') }}",
                    data:{id:id},
                    dataType: 'json',
                    success: function(data) {
                        toastr.success(data.message);
                            //$('#'+id).html('Disapproved');
                            resetfeeds();
                      },
                      error: function(e) {
                        console.log(e.message);
                      }
                    });
                }
             });     
    return ;
    }

    function searchfeeds(){
        var filtercount=0;
        jQuery('.abl-filter').each(function(){
            if(jQuery(this).val().length>0){
                filtercount++;
            }
        });
        if(filtercount==0){
            Command: toastr.error("Select some filter to search!");
            return false;   
        }
        var sr_dt=btoa(jQuery('.abl-filter').serialize());
        var url='{{ URL::to('admin/photoCompetition') }}';
        getGridData(url,'grid_container','sr_dt='+sr_dt+'&cp=1');
    }

    function resetfeeds(){
        var url='{{ URL::to('admin/photoCompetition') }}';
        getGridData(url,'grid_container','&cp=1');                    
    }
    
    jQuery(function () {
        jQuery('body').off().on('click', '#grid_container .pagination a', function (e) {
        e.preventDefault();
        var url = jQuery(this).attr('href');
        getGridData(url, 'grid_container','{{(isset($sr_dt)&&count($sr_dt))?"sr_dt=".Helper::convertSearchDataEncode($sr_dt):""}}');
        window.history.pushState("", "", url);
    });

});            
</script>