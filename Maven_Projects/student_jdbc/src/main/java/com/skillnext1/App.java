package com.skillnext1;

import java.util.*;

public class App {
    public static void main(String[] args) throws Exception 
	{
        Scanner sc = new Scanner(System.in);
        StudentDAO dao = new StudentDAO();

        while (true) 
		{
            System.out.println("\n=== Student Management System ===");
            System.out.println("1. Insert Student");
            System.out.println("2. Update Student");
            System.out.println("3. Delete Student");
            System.out.println("4. View All Students");
            System.out.println("5. Exit");
            System.out.print("Enter your choice: ");

            int ch = sc.nextInt();

            switch (ch) 
			{

            case 1:
                Student s = new Student();
                System.out.print("Enter Name: ");
                s.setName(sc.next());
                System.out.print("Enter Semester: ");
                s.setSem(sc.nextInt());
                System.out.print("Enter Dept: ");
                s.setDept(sc.next());
                dao.insert(s);
                System.out.println("Inserted Successfully!");
                break;

            case 2:  // UPDATE
                int uid;
                while (true) 
				{
                    System.out.print("Enter ID to Update: ");
                    uid = sc.nextInt();

                    if (dao.exists(uid)) break;
                    System.out.println("ID not present! Enter again.");
                }

                Student s2 = new Student();
                s2.setId(uid);

                System.out.print("Enter New Name: ");
                s2.setName(sc.next());
                System.out.print("Enter New Sem: ");
                s2.setSem(sc.nextInt());
                System.out.print("Enter New Dept: ");
                s2.setDept(sc.next());

                dao.update(s2);
                System.out.println("Updated Successfully!");
                break;

            case 3:  // DELETE
                int did;
                while (true) 
				{
                    System.out.print("Enter ID to Delete: ");
                    did = sc.nextInt();

                    if (dao.exists(did)) break;
                    System.out.println("ID not present! Enter again.");
                }

                dao.delete(did);
                System.out.println("Deleted Successfully!");
                break;

            case 4:
                List<Student> list = dao.selectAll();
                System.out.println("\n--- Student Records ---");
                for (Student st : list) 
				{
                    System.out.println(st);
                }
                break;

            case 5:
                System.out.println("Exiting...");
                System.exit(0);

            default:
                System.out.println("Invalid Choice!");
            }
        }
    }
}
