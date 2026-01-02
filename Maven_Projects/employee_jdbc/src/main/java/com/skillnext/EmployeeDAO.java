package com.example;

import java.sql.*;
import java.util.*;

public class EmployeeDAO {

    private static final String URL = "jdbc:mysql://localhost:3306/skillnext_db";
    private static final String USER = "root";
    private static final String PASSWORD = "root"; // change this

    // Add employee
    public void addEmployee(Employee emp) throws Exception {
        Connection conn = DriverManager.getConnection(URL, USER, PASSWORD);
        String sql = "INSERT INTO employee (name, email, salary) VALUES (?, ?, ?)";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setString(1, emp.getName());
        stmt.setString(2, emp.getEmail());
        stmt.setDouble(3, emp.getSalary());
        stmt.executeUpdate();
        conn.close();
    }

    // Fetch all employees
    public List<Employee> getAllEmployees() throws Exception {
        Connection conn = DriverManager.getConnection(URL, USER, PASSWORD);
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("SELECT * FROM employee");

        List<Employee> list = new ArrayList<>();
        while (rs.next()) {
            Employee e = new Employee();
            e.setId(rs.getInt("id"));
            e.setName(rs.getString("name"));
            e.setEmail(rs.getString("email"));
            e.setSalary(rs.getDouble("salary"));
            list.add(e);
        }
        conn.close();
        return list;
    }

    // Delete employee
    public void deleteEmployee(int id) throws Exception {
        Connection conn = DriverManager.getConnection(URL, USER, PASSWORD);
        String sql = "DELETE FROM employee WHERE id=?";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setInt(1, id);
        stmt.executeUpdate();
        conn.close();
    }

    // Update employee
    public void updateEmployee(Employee emp) throws Exception {
        Connection conn = DriverManager.getConnection(URL, USER, PASSWORD);
        String sql = "UPDATE employee SET name=?, email=?, salary=? WHERE id=?";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setString(1, emp.getName());
        stmt.setString(2, emp.getEmail());
        stmt.setDouble(3, emp.getSalary());
        stmt.setInt(4, emp.getId());
        stmt.executeUpdate();
        conn.close();
    }
}
