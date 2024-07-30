<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      display: block;
      margin-right: 120px;
      margin-left: 120px;
    }

    p {
      padding: 5px;
      margin: 0;
    }

    .title {
      margin: auto;
      width: 75%;
      text-align: center;
    }

    .title hr {
      width: 250px;
    }

    .risk, .disclaimer, .regist {
      padding: 30px;
      margin-bottom: 30px;
    }
    .container-flex {
      display:flex;
      flex-wrap: wrap;
    }

    .items-flex > :is(p,) {
      display: inline;
      width: 50%;
    }
  </style>
  <title>Document</title>
</head>
<body>
  <div class="header">
    <div class="title">
      <h2>SURYA BINTANG</h2><hr>
      <h3>ADVENTURES</h3>
    </div>
  </div><br><br>

  @foreach ($registrations as $registration)
  <hr>
  <div class="regist">
    <div class="title">
      <h2>REGISTRATION FORM {{ $loop->index + 1 }}</h2>
    </div>
    <div class="body">
      <div class="form">
        <p>Guest Name     : {{ $registration->name }}</p>
        <p>Telephone      : {{ $registration->phone }}</p>
      </div>
      <br>
      <p>Who to contact in case of emergency?</p>
      <div class="form">
        <p>Name  : {{ $registration->emergency_contact_name }}</p>
        <p>Email : {{ $registration->emergency_contact_email }}</p>
        <p>Phone : {{ $registration->emergency_contact_phone }}</p>
      </div>
      <br><div class="medical">
        <h3>MEDICAL INFORMATION</h3>
        <p>Surya Bintang Adventures an active and physically demanding trip. Please give us complete information about yourself so that we may serve you
          better
        </p>
        <div class="form">
          <p>Your date of birth : {{ $registration->date_of_birth }}</p>
          <p>Sex : {{ $registration->sex }}</p>
        </div>
        <p>Medical concern : {{ $registration->medical_note }}</p>
        <br><br>
        <strong>Note : Please read carefully the exclusions in personal accident Insurance police afforded by Surya Bintang Adventures </strong>
      </div> 
      <!-- end medical -->
    </div>
  </div>
  <!-- end regist form -->

  <div style="page-break-before:always;">
    <div class="title">
      <h2>PARTICIPANT ACKNOWLEDGEMENT OF RISK</h2>
    </div>
    <div class="body">
      <p>I understand and acknowledge that activity I am about to voluntarily engage in as a participant bears certain known and unanticipated risk while could result injury, death, illness or disease. Physical damage to may property and belongings.
      </p>
      <p>Participant understanding and acceptance of insurance coverage. <br>
        I confirm that I have seen if fully copy of the accident policy that Surya Bintang Adventures have afforded me and that I was given both the opportunity and the time to read the policy and that fully understand and accept the coverage benefits, the terms. the conditions and all the exclusion 
      </p>
      <p>Participant waiver <br>
        I herby voluntary waiver, release, forever discharge and agree to harmless and indemnity Surya Bintang Adventures its agents or employees, and all other persons or entities from any all liability claim, demands, actions or right faction, which are anyway connected to my participant in the activity for any injury, death, illness or disease, injuries to myself and damage to my property order than a legitimate personal accident policy afforded by as a result of my own injuries sustained during the activity I hereby give permission for to us photographs an or video coverage of myself, taken whilst engaged in this activity, for promotional purpose, without charge and without any consultation With my self.</p>
      <br>
      <p>Signature of participants :</p>
      <br>
      <br>
      <br>
      <p>Date :</p>
      <p>If under 7 years old signature of parent of guardian :</p>
      <br>
      <br>
      <br>
    </div>
  </div>
  <!-- end of risk -->
  
  <div style="page-break-before: always;">
    <div class="title">
      <h2>DISCLAIMER / RELEASE OF LIABILITY</h2>
    </div>
    <div class="body">
      <p>I agree that I assume all risk of injury, loss or damage during participation in Surya Bintang Adventures (Rafting. Cycling, Trekking, Swing, Touring/ATV, atc). I hereby agree that Surya Bintang Adventures is not responsible for any injury, loss, death or damage. Release of liability und assumption of risk agreement based on the following .</p>
      <ol>
        <li>I. am 5 years old or under, or 65 years old over.</li>
        <li>I am taking medication or I am under the influence of drugs or alcohol.</li>
        <li>I have a physical condition which might effect my safety or health while joining an activity (for example : heart problems, asthma, recently, broken bones, etc).</li>
        <li>I am pregnant.</li>
        <li>I am currently under doctor's care.</li>
        <li>I may have difficulty walking down or up the steps to or from the river (Approximately 10-20 minutes walk each way).</li>
      </ol>
      <p>I the undersigned, hereby acknowledge that I have voluntarily chosen to go with Surya Bintang Adventures and understand that certain risk are inherent in any recreational activity and cannot be eliminated, altered or controlled. The risk that contributes to the unique character of the activity can also be the cause of physical or mental Injury, illness, death, loss or damages and also the damage or loss of personal property. In signing this document I fully recognize and understand that if I (or a minor on whose behalf I am signing this release) gets hurt/dies. I, my family relatives and/or acquaintance would be giving up the right to make a claim or file a lawsuit against Surya Bintang Adventures, it's employee/staff or the booking agent (person/Company).</p>
      <div class="form">
        <p>Customer's name and signature : </p>
        <br>
        <br>
        <br>
        <p>Activity                      : {{ $order_detail->name }}</p>
      </div>
      <p>I, the undersigned, hereby acknowledge that I have voluntarily chosen to go Surya Bintang Adventures white water rafting tour and understand that certain risks are inherent in may recreational activity and cannot be eliminated, altered, or controlled. The risks that contribute to the unique character of the activity can also be the cause of physical or mental Injury, illness, death, loss or damages and also the damage or loss personal property. In signing this document, I fully recognize and understand that if I (or any minor of whose behalf I am signing this release) was hurt die or my property was lost or damaged. I, my family, relatives and / or acquaintance would be giving up the right to make a claim or file a lawsuit against Surya Bintang Adventures employees/staff or the booking agent (person).</p>
      <br>
      <table style="width: 100%; font-weight:normal">
        <th style="width: 50%;">
          <p>Participant's Signature :</p>
          <br>
          <br>
          <br>
          <p> Activity Date : </p>
        </th>
        <th style="width: 50%;">
          <p>Guardian's Signature	 :</p>
          <br>
          <br>
          <br>
          <p> Activity Date : </p>
        </th>
      </table>
    </div>
  </div>
  <div style="page-break-after:always;page-break-before: always;">
  @endforeach
</body>
</html>