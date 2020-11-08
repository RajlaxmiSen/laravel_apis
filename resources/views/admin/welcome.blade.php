@extends('admin/admin_master')
@section('page_title')
<h1>
    Dashboard
    <small>
        a small overview of system
    </small>
</h1>
@endsection

@section('breadcrumbs')
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a href="{{URL::to('/admin')}}">
          Home
        </a>
    </li>
</ul>
@endsection

@section('container')
<div id="dashboard">
  <div class="col-md-12">
  <div class="row">
    <div class="col-md-12">
    <div class="portlet  light bordered">
      <div class="portlet-title">
        <div class="caption">
          <i class="fa fa-arrow-right font-dark hide"></i>
          <span class="caption-subject font-red  sbold bold uppercase">Dashboard</span>
        </div>
        <div class="actions">
              
                <i class="fa fa-calendar"></i>
                <input type="text" name="date_range" id="date_range" value="">
             
                <button class="btn btn-primary" id="filter" onclick="getStudioData()">Filter</button>
             
        </div>
      </div>
    <div class="portlet-body">
    <div class="row easy-pie-chart-row" style="margin-top: 5px;">
       {{--  <div class="col-md-12"> --}}
          
        
        {{-- <div class="col-md-4"> --}}
            <div class="easy-pie-chart easy-pie-chart-invoice-payment ">
              <div class=" bounce" data-percent="46">
              <i class="fa fa-comments" aria-hidden="true"></i>
                <span id="voucher_amount">0</span><!-- <br>
                <i class="fa fa-money" aria-hidden="true"></i> -->

              </div>
              <a class="title" href="javascript:;">Feed Count
                
              </a>
            </div>
        {{-- </div>    --}} 
       {{--  <div class="margin-bottom-10 visible-sm"> </div> --}}
        {{-- <div class="col-md-3"> --}}
            <div class="easy-pie-chart easy-pie-chart-invoice-payment" >
              <div class=" bounce" data-percent="46">
              <i class="fa fa-user" aria-hidden="true"></i>
                <span id="credit_amount">0</span><!-- <br>
                <i class="fa fa-money" aria-hidden="true"></i> -->

              </div>
              <a class="title" href="javascript:;"> User Count
                
              </a>
            </div>
        {{-- </div>    --}} 
       {{--  <div class="margin-bottom-10 visible-sm"> </div> --}}
        {{-- <div class="col-md-3"> --}}
            <div class="easy-pie-chart easy-pie-chart-invoice-payment" >
              <div class=" bounce" data-percent="46">
              <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                <span id="card_amount">0</span><!-- <br>
                <i class="fa fa-money" aria-hidden="true"></i> -->

              </div>
              <a class="title" href="javascript:;">Likes Count
                
              </a>
            </div>
        {{-- </div> --}}    
        {{-- <div class="margin-bottom-10 visible-sm"> </div> --}}
       {{--  <div class="col-md-3"> --}}
            <div class="easy-pie-chart easy-pie-chart-invoice-payment">
              <div class=" bounce" data-percent="46">
              <i class="fa fa-picture-o" aria-hidden="true"></i>
                <span id="cash_amount">0</span>
              </div>
              <a class="title" href="javascript:;"> Total Photo's for Competition
                
              </a>
            </div>
        {{-- </div>    --}} 
        
      
        {{-- </div> --}}
      </div>
    </div>
  </div>      
</div>
  </div>

  </div>
  <div class="clearfix"></div>
  <div class="col-md-12">
    <div class="row">
      
    </div>
  </div>
</div>
<style>
.table-container {
  height: 257px;
}
#upcoming_order.table-container {
  height: 560px;
}
#upcoming_order td{
  text-align: center;
}


.portlet.light>.portlet-title {
    padding: 0px 11px;
}
.portlet.light{
  padding: 5px 0px 0px;
}
.easy-pie-chart-invoice-payment{
    width: 300px!important;
}
.portlet-fixed-height{
  height: 300px;
}
#dashboard{
  margin-top:10px;
}
.card-status-parent-custom .card-status{
  padding: 3px 7px;
}
.dashboard-stat.blue,.dashboard-stat.red,.dashboard-stat.purple{
   box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
   color: #fff;
}
.dashboard-stat.blue:hover,.dashboard-stat.red:hover,.dashboard-stat.purple:hover{
   box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}
.Upcoming-order-caption{
  display: flex!important;
    justify-content: space-between!important;
    float:none!important;
}
/* .total-box #total_upcoming_count{
  padding: 0px 40px!important;
} */
.total-box{
  font-size: 16px;
}
.revenue-box{
  display: flex;
    justify-content: center;
    padding: 0px;
}

.revenue-item{
  display: flex;
    flex-direction: column;
    align-items: center;
    flex-basis: 47%;
    margin: 5px;
    border: 1px solid #fff;
   
}
.dashboard-stat{
  height: 210px!important;
}
.card-status-parent{
  display: flex;
  justify-content: center;
}
.card-status{
 box-shadow: 2px 1px 0px #e6e6e6;
  margin: 4px!important; 
  padding: 0px 4px;
}
.card-status:hover{
  background-color:#fff!important;
  color: #3598dc!important;
}
.card-status p{
  margin: 0px!important;
  padding: 7px;
  color: #000!important;
}
.revenue-card{
  margin-bottom: 13px; 
}

.revenue-content{
   text-align: center;
   border: 1px solid #eee!important;
}
.dashboard-stat .details{
  padding-right:0px;
}
.dashboard-stat.red .details,.dashboard-stat .details{
    position: static!important;
    color: #fff;
}
.dashboard-stat{
text-decoration: none!important;
}
.dashboard-stat .visual{
  height: 42px;
}
.dashboard-stat .details .number{
  font-size: 29px;
  padding: 5px 12px 0px 0px;
}
.fa-rupee{
  font-size:17px;
}
.easy-pie-chart-row{
  display: flex;
  padding: 5px;
  flex-wrap: wrap;
  justify-content: center;
}
.easy-pie-chart{
      box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  margin: 7px 5px;
    width: 19%;
    padding: 9px 0px;
    word-break: break-all;

}
.easy-pie-chart-collection{
  text-align:left;
}
.easy-pie-chart-collection .bounce{
  text-align:center;
}
.easy-pie-chart span{
  font-size: 17px;
}
.easy-pie-chart .title{
  font-size: 13px;
}
.easy-pie-chart:hover{
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}
.fa-arrow-down{
  color:red;
}
.fa-arrow-up{
  color:green;
}
.collect_payment .easy-pie-chart{
      width: 15.7%;
}
</style>
<script type="text/javascript">
  $('document').ready(function(){
        $('#date_range').datepicker({
            format: 'yyyy-mm-dd',
            endDate: '+0d',
            autoclose: true
        });
        $('#date_range').datepicker("setDate", new Date());
});

</script>
@endsection
