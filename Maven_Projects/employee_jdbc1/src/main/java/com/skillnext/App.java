package com.skillnext;

import java.util.*;

public class App {
    public static void main(String[] args) {
        try {
            EmployeeDAO dao = new EmployeeDAO();

            // Add employee
            Employee e1 = new Employee("John Doe", "john@example.com", 50000);
            dao.addEmployee(e1);
            System.out.println("Employee Added Successfully!");

            // Fetch employees
            List<Employee> employees = dao.getAllEmployees();
            for (Employee e : employees) {
                System.out.println(e);
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
