<?php

class Team implements EmployeeInterface
{
    private $employees = [];
    private $teamName = '';

    public function __construct($teamName)
    {
        $this->teamName = $teamName;
    }

    public function getTeamName()
    {
        return $this->teamName;
    }

    public function addEmployee(EmployeeInterface $obj)
    {
        if (!in_array($obj, $this->employees)) {
            $this->employees[] = $obj;
        } else {
            return;
        }
    }

    public function getSalary()
    {
        $result = 0;
        foreach ($this->employees as $employee) {
            $result += $employee->getSalary();
        }
        return $result;
    }
}
