package com.skillnext;

/**
 * Hello world!
 *
 */
 import java.util.*;
public class App 
{
	public static boolean check_prime(int n)
	{
		for(int i=2; i <= Math.sqrt(n); i++)
		{
			if(n % i ==0)
				return false;
		}
		return true;
		
	}
    public static void main( String[] args )
    {
        Scanner sc= new Scanner(System.in);
		System.out.println("Enter a number ");
		int n = sc.nextInt();
		if(check_prime(n))
		{
			System.out.println("Num = "+n + " is prime");
		}
		else
		{
			System.out.println("Num = "+n + " is NOT prime");
		}
    }
}
