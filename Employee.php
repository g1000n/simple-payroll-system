<?php
class Employee {
    private $emp_id;
    private $emp_name;
    private $date_hired;
    private $dept_id;
    private $position_id;
    private $designation;
    private $employment_status;
    private $hours_worked;
    private $rate_per_hour;
    private $ot_hours;

    // constructor
    public function __construct($emp_id, $emp_name, $date_hired, $dept_id, $position_id, $designation, 
                                $employment_status, $hours_worked, $rate_per_hour, $ot_hours) {
        $this->emp_id = $emp_id;
        $this->emp_name = $emp_name;
        $this->date_hired = $date_hired;
        $this->dept_id = $dept_id;
        $this->position_id = $position_id;
        $this->designation = $designation;
        $this->employment_status = $employment_status;
        $this->hours_worked = $hours_worked;
        $this->rate_per_hour = $rate_per_hour;
        $this->ot_hours = $ot_hours;
    }

    // compute Gross Pay which is not stored in the DB
    public function computeGrossPay() {
        $regular_pay = $this->hours_worked * $this->rate_per_hour;
        $overtime_pay = ($this->ot_hours > 0) ? ($this->ot_hours * $this->rate_per_hour * 1.5) : 0;
        return $regular_pay + $overtime_pay;
    }

    public function getEmployeeDetails() {
        $details = [
            "emp_id" => $this->emp_id,
            "emp_name" => $this->emp_name,
            "date_hired" => $this->date_hired,
            "dept_id" => $this->dept_id,
            "position_id" => $this->position_id,
            "designation" => $this->designation,
            "employment_status" => $this->employment_status,
            "hours_worked" => $this->hours_worked,
            "rate_per_hour" => $this->rate_per_hour,
            "ot_hours" => $this->ot_hours
        ];
        return $details;
    }
    

    // setter methods (if needed) setter methods for when needed
    public function setDepartment($dept_id) {
        $this->dept_id = $dept_id;
    }

    public function setPosition($position_id) {
        $this->position_id = $position_id;
    }
}
?>
