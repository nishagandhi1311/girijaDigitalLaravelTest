<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Validator;

class DynamicTimeTableController extends Controller
{

    /**
     * Get the No of Working days,No of Subjects per day,Total Subjects data and Calculate Hours For Week.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CalculateHoursForWeek(Request $request)
    {
        $request->validate([
            'working_days'        => 'required|integer|min:1|max:7',
            'subject_per_day'     => 'required|integer|max:9',
            'total_subject'       => 'required|integer'
        ]);
        $request->session()->put('working_days', $request->working_days);
        $request->session()->put('subject_per_day', $request->subject_per_day);
        $request->session()->put('total_subject', $request->total_subject);
        $total_hoursfor_week = $request->working_days * $request->subject_per_day;
        $request->session()->put('total_hoursfor_week', $total_hoursfor_week);
        return redirect()->route('dynamicTimeTableStubjectForm');
    }

    /**
         * Get the Subject Name,Total hours of each subject for total working days and create Dynamic Time Table.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
    */
    public function CreateTimeTable(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'subject_name.*.name' => 'required',
            'subject_hours.*.hours' => 'required|integer',
        ]);
        
        $validator->after(function ($validator) {
            foreach ($validator->getData()['subject_hours'] as $key => $value) {
                foreach ($value as  $key1 => $items) {
                    $subject_hours[] = $items;
                }
            }
            $sum = array_sum($subject_hours);
            if ($sum !== session()->get('total_hoursfor_week')) {
                $validator->errors()->add('subject_hours.*.hours', 'Make sure the sum of all fields equals '.session()->get('total_hoursfor_week'));
            }
        });
        if ($validator->fails()) {
            return redirect('dynamicTimeTableStubjectForm')
                        ->withErrors($validator)
                        ->withInput();
        }
        $working_days = session()->get('working_days');
        $subject_per_day = session()->get('subject_per_day');
        foreach ($request->subject_name as $key => $value) {
            foreach ($value as  $key1 => $items) {
                $subject_name[] = $items;
            }
        }
        foreach ($request->subject_hours as $key => $value) {
            foreach ($value as  $key1 => $items) {
                $subject_hours[] = $items;
            }
        }
        $subject_name_hours=array_combine($subject_name,$subject_hours);
        for($i=0;$i<$working_days;$i++)
        {
            for($j=0;$j<$subject_per_day;$j++)
            {
                $random = Arr::random($subject_name);
                $time_table[$j][$i] = $random;
                
                $subject_name_hours[$random] = (int)$subject_name_hours[$random] - 1;
            
                if($subject_name_hours[$random] == 0)
                {
                    $subject_name_key = array_keys($subject_name,$random)[0];
                    $subject_name = Arr::except($subject_name,[$subject_name_key]);
                    $subject_name_hours = Arr::except($subject_name_hours,[$random]);
                }
            }
        }
        $request->session()->put('time_table', $time_table);
        return redirect()->route('TimeTable');
    }
   
}
