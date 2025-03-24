<?php
class Employee {
    // Fields or attributes
    private $emp_id;
    private $emp_name;
    private $date_hired;
    private $dept;
    private $position;
    private $designation;
    private $employment_status;
    private $hours_worked;
    private $rate_per_hour;
    private $ot_hours;

    // Constructor
    public function __construct($emp_id, $emp_name, $date_hired, $dept, $position, $designation, $employment_status, $hours_worked, $rate_per_hour, $ot_hours) {
        $this->emp_id = $emp_id;
        $this->emp_name = $emp_name;
        $this->date_hired = $date_hired;
        $this->dept = $dept;
        $this->position = $position;
        $this->designation = $designation;
        $this->employment_status = $employment_status;
        $this->hours_worked = $hours_worked;
        $this->rate_per_hour = $rate_per_hour;
        $this->ot_hours = $ot_hours;
    }

    // To compute pay
    public function computeGrossPay() {
        $regular_pay = $this->hours_worked * $this->rate_per_hour;
        $overtime_pay = $this->ot_hours * $this->rate_per_hour * 1.3;
        return $regular_pay + $overtime_pay;
    }
    

    // To get details
    public function getEmployeeDetails() {
        return [
            "ID" => $this->emp_id,
            "Name" => $this->emp_name,
            "Date Hired" => $this->date_hired,
            "Department" => $this->dept,
            "Position" => $this->position,
            "Designation" => $this->designation,
            "Employment Status" => $this->employment_status,
            "Hours Worked" => $this->hours_worked,
            "Rate Per Hour" => $this->rate_per_hour,
            "OT Hours" => $this->ot_hours
        ];
    }
}
?>
