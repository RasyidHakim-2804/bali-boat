<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>E-Ticket Fast Boat</title>
        <style>
            @font-face {
                font-family: 'Montserrat';
                src: url('../storage/fonts/montserrat.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            body {
                font-family: 'Montserrat', Arial, sans-serif;
                font-size: 12px;
                margin: 0;
                margin-top: -50px;
                padding-bottom: 32px;
                padding-right: 40px;
                padding-left: 40px;
                color: #121212;
            }
            .opacity-light {
                opacity: 0.5;
            }
            .opacity-medium {
                opacity: 0.7;
            }
            .font-bold {
                font-weight: 400;
            }
            .font-medium {
                font-weight: 400;
            }
            .font-tiny {
                font-size: 6px;
            }
            .inline {
                display: inline;
            }
            .block {
                display: block;
            }
            .inline-block {
                display: inline-block;
            }
            .grid-col2 {
                display: grid;
                grid-template-columns: auto auto;
                gap: 10px;
            }
            table {
                width: 100%;
            }
            hr {
                border-color: #dddddd;
            }
            
            .header {
                text-align: left;
                padding-bottom: 10px;
                margin-bottom: 30px;
            }
            .header .title {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .logo {
                width:150px;
                display: block;
                margin-top: -20px;
                margin-left: -10px;
                margin-bottom: -40px;
            }
            .section {
                margin-bottom: 10px;
                margin-top: 10px;
            }
            .mb-1 {
                margin-bottom: 10px;
            }
            .card {
                border-radius: 5px;
                border-style: solid;
                border-width: 1px;
                border-color: #dddddd;
                margin-bottom: 10px;
                margin-top: 10px;
                padding-right: 15px;
                padding-left: 15px;
                padding-bottom: 10px;
                padding-top: 10px;
            }
            .highlight {
                font-weight: 1000;
                color: #00A59A;
            }
            .ticket-container {
                padding: 50px;
            }
        </style>
    </head>
    <body class="ticket-container">
        <div>
            <div class="header">
                <img src="{{public_path() . '/storage/assets/images/logo_black.png'}}" alt="Bali Boat" class="logo">
                <div class="title font-bold block">E-Ticket Fast Boat</div>
                <div class="inline-block">
                    <div class="inline opacity-light">Order ID:</div>
                    <div class="inline font-bold"> {{ $fastBoatOrder['user']['uuid']}}</div>
                </div>
            </div>

            {{-- Itinerary --}}
            <div class="section">
                <div class="font-bold">Itinerary</div>
                <hr>
                <div class="card">
                    <div class="font-bold opacity-light">Departure</div>
                    <br>
                    
                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Ship Name</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->first()->name}}</div>
                    </div>
                    
                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Date</div>
                        <div class="font-bold">{{ $fastBoatOrder->activity_date}}</div>
                    </div>

                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Time</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->first()->departure_time}}</div>
                    </div>

                    <div>
                        <div class="font-tiny opacity-light">Destination</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->first()->departure_harbor_name}} - {{ $fastBoatOrder->details->first()->arrival_harbor_name}}</div>
                    </div>
                                    
                </div>

                @if ($fastBoatOrder->details->count() > 1)
                <div class="card">
                    <div class="font-bold opacity-light">Return</div>
                    <br>
                    
                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Ship Name</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->last()->name}}</div>
                    </div>
                    
                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Date</div>
                        <div class="font-bold">{{ $fastBoatOrder->activity_date }}</div>
                    </div>

                    <div class="mb-1">
                        <div class="font-tiny opacity-light">Time</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->last()->departure_time}}</div>
                    </div>

                    <div>
                        <div class="font-tiny opacity-light">Destination</div>
                        <div class="font-bold">{{ $fastBoatOrder->details->last()->departure_harbor_name}} - {{ $fastBoatOrder->details->last()->arrival_harbor_name}}</div>
                    </div>
                                    
                </div>
                @endif
            </div>

            {{-- Passenger Detail --}}
            <div class="section">
                <div class="font-bold">Passenger Details</div>
                <hr>
                <table>
                    @foreach ($fastBoatOrder->forms as $forms)
                    <tr class="mb-minus-10">
                        <td>
                            <div class="card" style="page-break-before:auto;">
                                <div class="font-bold opacity-light">Passenger @iterator_count - {{$forms->first()->age_type}}</div>
                                <br>
                                
                                <div class="mb-1">
                                    <div class="font-tiny opacity-light">Full Name</div>
                                    <div class="font-bold">{{$forms->first()->name}}</div>
                                </div>
        
                                <div class="mb-1">
                                    <div class="font-tiny opacity-light">Nationality</div>
                                    <div class="font-bold">{{$forms->first()->nationality}}</div>
                                </div>
        
                                <div>
                                    <div class="font-tiny opacity-light">ITAS Holder</div>
                                    <div class="font-bold">
                                        @if ($forms->first()->is_itas_holder)
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($forms->count() > 1)
                            <div class="card" style="page-break-before:auto;">
                                <div class="font-bold opacity-light">Passenger 1 - {{$forms->last()->age_type}}</div>
                                <br>
                                
                                <div class="mb-1">
                                    <div class="font-tiny opacity-light">Full Name</div>
                                    <div class="font-bold">{{$forms->last()->name}}</div>
                                </div>
        
                                <div class="mb-1">
                                    <div class="font-tiny opacity-light">Nationality</div>
                                    <div class="font-bold">{{$forms->last()->nationality}}</div>
                                </div>
        
                                <div>
                                    <div class="font-tiny opacity-light">ITAS Holder</div>
                                    <div class="font-bold">
                                        @if ($forms->last()->is_itas_holder)
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>

            {{-- Important Information --}}
            <div class="section">
                <div class="font-bold">Important Information</div>
                <hr>
                <ul class="opacity-medium">
                    <li>
                        Please check-in at least <span class="highlight">60 minutes</span> before departure time at the appropriate fast boat counter.
                    </li>
                    <li>
                        Passengers are required to show proof of identity along with this e-voucher.
                    </li>
                    <li>
                        Passengers are required to show ITAS Card if you buy tickets as an ITAS Holder.
                    </li>
                    <li>
                        Please note there are additional retribution fees applicable for certain destinations:
                    </li>
                    <ul>
                        <li>
                            Nusa Penida: Adult IDR 25,000, Child IDR 15,000.
                        </li>
                        <li>Padang Bay: IDR 10,000 per person. Pay cash upon arrival.
                        </li>
                        <li>
                            Gili Islands: ARRIVAL - IDR 20,000 per person, DEPARTURE - IDR 20,000 per person. Pay cash upon arrival & departure.
                        </li>
                    </ul>
                    <li>
                        Each passenger is allowed to carry a maximum of two pieces of luggage, free of charge, with a combined weight not exceeding 20 kg.
                    </li>
                    <li>
                        All timing listed are WITA (Central Indonesian Time) / UTC+08:00.
                    </li>
                    <li>
                        Reschedule and cancellation conditions follow the provisions from Bali Boat.
                    </li>
                    <li>
                        Failure to adhere to check-in or boarding times may result in missing your boat and forfeiting the fare paid.
                    </li>
                </ul>
            </div>

            <div class="footer">
                &copy; 2024 Bali Boat. All rights reserved.
            </div>
        </div>
    </body>
</html>