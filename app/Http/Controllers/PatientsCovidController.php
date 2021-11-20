<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientsStatus;
use App\Models\Status;

class PatientsCovidController extends Controller
{
    //STATUC CODE
    public $RequestSucceeded = 200;
    public $ResourceCreated = 201;
    public $NoContent = 204;
    public $Unauthenticated = 401;
    public $ResourceNotFound = 404;

    public function index()
    {
        $patients = PatientsStatus::join(
            'patients',
            'patients.id',
            '=',
            'patients_statuses.patient_id'
        )
            ->join(
                'statuses',
                'statuses.id',
                '=',
                'patients_statuses.status_id'
            )
            ->get([
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
            ]);

        if ($patients->count() != 0) {
            $response['message'] = 'Get All Resource!';
            $response['data'] = $patients;
        } else {
            $response['message'] = 'Data is empty!';
        }
        return response()->json($response, $this->RequestSucceeded);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'status_id' => 'required',
            'date_in' => 'required',
            'date_out' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                $this->ResourceNotFound
            );
        }

        $patient = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ];

        $createPatient = Patient::create($patient);

        $patientStatuses = [
            'patient_id' => $createPatient->id,
            'status_id' => $request->status_id,
            'date_in' => $request->date_in,
            'date_out' => $request->date_out,
        ];

        $createPatientsStatuses = PatientsStatus::create($patientStatuses);

        $data = [
            'id' => $createPatientsStatuses->id,
            'name' => $createPatient->name,
            'phone_number' => $createPatient->phone_number,
            'address' => $createPatient->address,
            'status' => Status::select('name')
                ->where('id', $createPatientsStatuses->status_id)
                ->get()[0]->name,
            'date_in' => $createPatientsStatuses->date_in,
            'date_out' => $createPatientsStatuses->date_out,
            'created_at' => $createPatientsStatuses->created_at,
            'updated_at' => $createPatientsStatuses->updated_at,
        ];

        $response['message'] = 'Resource is added successfully';
        $response['data'] = $data;

        return response()->json($response, $this->ResourceCreated);
    }

    public function show($id)
    {
        $patient = PatientsStatus::join(
            'patients',
            'patients.id',
            '=',
            'patients_statuses.patient_id'
        )
            ->join(
                'statuses',
                'statuses.id',
                '=',
                'patients_statuses.status_id'
            )
            ->where('patients_statuses.id', '=', $id)
            ->get([
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
            ]);

        if ($patient->count() != 0) {
            $response['message'] = 'Get Detail Resource!';
            $response['data'] = $patient[0];
        } else {
            $response['message'] = 'Resource not found';
            return response()->json($response, $this->ResourceNotFound);
        }

        return response()->json($response, $this->RequestSucceeded);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'status_id' => 'required',
            'date_in' => 'required',
            'date_out' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                $this->ResourceNotFound
            );
        }

        $patientStatuses = PatientsStatus::find($id);

        if ($patientStatuses != null) {
            $response['message'] = 'Get All Resource!';
            $response['data'] = $patientStatuses;
        } else {
            $response['message'] = 'Resource not found';
            return response()->json($response, $this->ResourceNotFound);
        }

        $patientParams = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ];

        $patient = Patient::find($patientStatuses->patient_id);
        $updatePatient = $patient->update($patientParams);

        $patientStatusesParams = [
            'patient_id' => $patientStatuses->patient_id,
            'status_id' => $request->status_id,
            'date_in' => $request->date_in,
            'date_out' => $request->date_out,
        ];

        $updatePatientStatuses = $patientStatuses->update(
            $patientStatusesParams
        );

        $data = [
            'id' => $patientStatuses->id,
            'name' => $patient->name,
            'phone_number' => $patient->phone_number,
            'address' => $patient->address,
            'status' => Status::select('name')
                ->where('id', $patientStatuses->status_id)
                ->get()[0]->name,
            'date_in' => $patientStatuses->date_in,
            'date_out' => $patientStatuses->date_out,
            'created_at' => $patientStatuses->created_at,
            'updated_at' => $patientStatuses->updated_at,
        ];

        $response['message'] = 'Resource is update successfully';
        $response['data'] = $data;

        return response()->json($response, $this->RequestSucceeded);
    }

    public function destroy($id)
    {
        $patientStatuses = PatientsStatus::find($id);

        if ($patientStatuses != null) {
            $response['message'] = 'Resource is delete successfully';
        } else {
            $response['message'] = 'Resource not found';
            return response()->json($response, $this->ResourceNotFound);
        }

        $patient = Patient::find($patientStatuses->patient_id);
        $patient->destroy($patientStatuses->patient_id);

        $patientStatuses->destroy($id);

        return response()->json($response, $this->RequestSucceeded);
    }

    public function search($name)
    {
        $patients = Patient::join(
            'patients_statuses',
            'patients_statuses.patient_id',
            'patients.id'
        )
        ->join(
            'statuses',
            'statuses.id',
            'patients_statuses.status_id'
        )
            ->select(
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
                )
            ->where('patients.name', $name)
            ->get();

        if ($patients->count() != 0) {

            $response['message'] = 'Get searched resource';
            $response['data'] = $patients;


        } else {
            $response['message'] = 'Resource not found!';
            return response()->json($response, $this->ResourceNotFound);
        }

        return response()->json($response, $this->RequestSucceeded);
    }

    public function positive()
    {
        $patients = Patient::join(
            'patients_statuses',
            'patients_statuses.patient_id',
            'patients.id'
        )
        ->join(
            'statuses',
            'statuses.id',
            'patients_statuses.status_id'
        )
            ->select(
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
                )
            ->where('statuses.id', 1)
            ->get();

        if ($patients->count() != 0) {
            $response['message'] = 'Get positive resource';
            $response['total'] = $patients->count();
            $response['data'] = $patients;
        } else {
            $response['message'] = 'Resource not found!';
            return response()->json($response, $this->ResourceNotFound);
        }

        return response()->json($response, $this->RequestSucceeded);
    }

    public function recovered()
    {
        $patients = Patient::join(
            'patients_statuses',
            'patients_statuses.patient_id',
            'patients.id'
        )
        ->join(
            'statuses',
            'statuses.id',
            'patients_statuses.status_id'
        )
            ->select(
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
                )
            ->where('statuses.id', 2)
            ->get();

        if ($patients->count() != 0) {
            $response['message'] = 'Get recovered resource';
            $response['total'] = $patients->count();
            $response['data'] = $patients;
        } else {
            $response['message'] = 'Resource not found!';
            return response()->json($response, $this->ResourceNotFound);
        }

        return response()->json($response, $this->RequestSucceeded);
    }

    public function dead()
    {
        $patients = Patient::join(
            'patients_statuses',
            'patients_statuses.patient_id',
            'patients.id'
        )
        ->join(
            'statuses',
            'statuses.id',
            'patients_statuses.status_id'
        )
            ->select(
                'patients_statuses.id',
                'patients.name',
                'patients.phone_number',
                'patients.address',
                'statuses.name as status',
                'patients_statuses.date_in',
                'patients_statuses.date_out',
                'patients_statuses.created_at',
                'patients_statuses.updated_at',
                )
            ->where('statuses.id', 3)
            ->get();

        if ($patients->count() != 0) {
            $response['message'] = 'Get positive resource';
            $response['total'] = $patients->count();
            $response['data'] = $patients;
        } else {
            $response['message'] = 'Resource not found!';
            return response()->json($response, $this->ResourceNotFound);
        }

        return response()->json($response, $this->RequestSucceeded);
    }
}
