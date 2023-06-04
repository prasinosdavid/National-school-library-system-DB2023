/* Querys for the adminstrator */
   /* 4.1.1 List with the total number of loans per school (Search criteria: year, calendar month, e.g. January).*/
            SELECT s.school_name, IFNULL(COUNT(br.rent_id),0) as total_loans
            FROM school s
            LEFT JOIN user u ON s.school_id = u.school_id
            LEFT JOIN book_rent br ON u.user_id = br.user_id AND YEAR(br.rent_date) = $year AND MONTH(br.rent_date) = $month
            GROUP BY s.school_id
            ORDER BY total_loans DESC;
	
    /* 4.1.2.For a given book category (user-selected), which authors belong to it and which teachers 
			 have borrowed books from that category in the last year? */
             
             SELECT DISTINCT a.author_first_name, a.author_last_name
			 FROM author a
			 JOIN book_author ba ON a.author_id = ba.author_id
			 JOIN book_category bc ON ba.book_id = bc.book_id
			 WHERE bc.category_id = $category_id;
             
             SELECT DISTINCT u.first_name, u.last_name
			 FROM user u
			 JOIN book_rent br ON u.user_id = br.user_id
			 JOIN book_category bc ON br.book_id = bc.book_id
			 WHERE bc.category_id = 1 AND u.role = 'teacher' AND YEAR(br.rent_date) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR);
             
	/*4.1.3. Find young teachers (age < 40 years) who have borrowed the most books and the number of books. */

             SELECT u.first_name, u.last_name, COUNT(br.book_id) as num_borrowed
			 FROM user u
			 JOIN book_rent br ON u.user_id = br.user_id
			 WHERE u.role = 'teacher' AND TIMESTAMPDIFF(YEAR, u.date_of_birth, CURDATE()) < 40
			 GROUP BY u.user_id
			 ORDER BY num_borrowed DESC;
             
	/*4.1.4. Find authors whose books have not been borrowed. */
             SELECT a.author_first_name, a.author_last_name
			 FROM author a
			 WHERE NOT EXISTS 
				(SELECT 1 FROM book_author ba
				 JOIN book_rent br ON ba.book_id = br.book_id
				 WHERE a.author_id = ba.author_id);

	/*4.1.5. Which operators have loaned the same number of books in a year with more than 20 loans? */
			
            /*finds the schools with more than 20 loans in the past 1 year */
            SELECT s.school_name, COUNT(br.rent_id) AS number_of_loans 
			FROM school AS s
			JOIN user AS u ON s.school_id = u.school_id
			JOIN book_rent AS br ON u.user_id = br.user_id
			WHERE br.rent_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
			GROUP BY s.school_id
			HAVING number_of_loans > 20;
            
            /* if there are schools with the same amount of loans run this query where $count=number_of_loans (the loans that are the same for the schools)
				, otherwise return no result*/
                
            /* this query returns for each duplicate number_of_loans, the distinct school duplicates */
            
            SELECT s.school_name, s.school_id, COUNT(br.rent_id) AS number_of_loans 
			FROM school AS s
			JOIN user AS u ON s.school_id = u.school_id
			JOIN book_rent AS br ON u.user_id = br.user_id
			WHERE br.rent_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
			GROUP BY s.school_id
			HAVING number_of_loans = $count;
            
            /* this query returns the name of the school operator where there are the same amount of loans.
            $school_id is fetched from the above query. This query is ran at least twice, to find both school operators*/
            
            SELECT u.first_name, u.last_name 
            FROM user AS u
            INNER JOIN school AS s ON s.school_id=u.school_id
            WHERE u.role='admin' AND u.school_id= $school_id;


	/* 4.1.6. Many books cover more than one category. Among field pairs (e.g., history and poetry) that 
		  are common in books, find the top-3 pairs that appeared in borrowings. */
			
            SELECT c1.category_name AS category_name1, c2.category_name AS category_name2, COUNT(*) as borrowings
			FROM book_rent br
			JOIN book_category bc1 ON br.book_id = bc1.book_id
			JOIN category c1 ON bc1.category_id = c1.category_id
			JOIN book_category bc2 ON br.book_id = bc2.book_id
			JOIN category c2 ON bc2.category_id = c2.category_id
			WHERE c1.category_id < c2.category_id
			GROUP BY c1.category_id, c2.category_id
			ORDER BY borrowings DESC
			LIMIT 3;

	/* 4.1.7. Find all authors who have written at least 5 books less than the author with the most books. */
			
            SELECT a.author_first_name, a.author_last_name, COUNT(ba.book_id) as num_books
			FROM author a
			JOIN book_author ba ON a.author_id = ba.author_id
			GROUP BY a.author_id
			HAVING num_books <= (SELECT MAX(num_books) - 5 FROM 
			(SELECT COUNT(ba.book_id) as num_books
			FROM author a
			JOIN book_author ba ON a.author_id = ba.author_id
			GROUP BY a.author_id) subquery)
			ORDER BY num_books DESC;
	
    
/* Querys for the school operators */

	/*4.2.1. All books by Title, Author (Search criteria: title/ category/ author/ copies). */
		
        SELECT b.*, (
            SELECT GROUP_CONCAT(DISTINCT CONCAT(a.author_first_name, ' ', a.author_last_name))
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id
          ) AS authors, (
            SELECT GROUP_CONCAT(DISTINCT c.category_name)
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id
          ) AS categories, (
            SELECT GROUP_CONCAT(DISTINCT k.keyword)
            FROM book_keywords bk
            LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
            WHERE bk.book_id = b.book_id AND k.keyword LIKE '$query%'
          ) AS keywords, bl.no_of_copies_in_library, bl.last_update
          FROM book_in_library bl
          LEFT JOIN book b ON bl.book_id = b.book_id
          WHERE bl.school_id = $school_id AND bl.no_of_copies_in_library > 0
          AND ( b.book_title LIKE '$query%' OR EXISTS (
            SELECT *
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id AND (a.author_first_name LIKE '$query%' OR a.author_last_name LIKE '$query%')
          ) OR EXISTS (
            SELECT *
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id AND (c.category_name LIKE '$query%' OR bl.no_of_copies_in_library LIKE '$query%')
          ));

	/* 4.2.2.Find all borrowers who own at least one book and have delayed its return. (Search criteria: 
	   First Name, Last Name, Delay Days). */
       SELECT u.first_name, u.last_name, br.rent_date, br.returned_at, b.book_title,
               (CASE
                    WHEN br.returned_at IS NULL AND br.rent_date < CURDATE() THEN DATEDIFF(CURDATE(), br.rent_date)
                    ELSE 0
                END) AS delay_days
        FROM user u
        LEFT JOIN book_rent br ON u.user_id = br.user_id
        LEFT JOIN book b ON br.book_id = b.book_id
        WHERE u.school_id = $school_id AND br.returned_at IS NULL AND br.rent_date < CURDATE()
        ORDER BY delay_days DESC;
        
	/* 4.2.3.Average Ratings per borrower and category (Search criteria: user/category) */
		
        SELECT u.user_id, u.first_name, u.last_name, c.category_name, AVG(r.rating) as avg_rating
            FROM user u
            LEFT JOIN book_rent br ON u.user_id = br.user_id
            LEFT JOIN review r ON br.rent_id = r.rent_id
            LEFT JOIN book_category bc ON br.book_id = bc.book_id
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE u.school_id = $school_id AND c.category_id = '$category_id $searchQuery'
            GROUP BY u.user_id
            ORDER BY u.first_name, u.last_name;

/* Querys for the users */

	/* 4.3.1.List with all books (Search criteria: title/category/author), ability to select a book and create 
	   a reservation request. */          
		
        SELECT b.*, GROUP_CONCAT(DISTINCT a.author_first_name, ' ', a.author_last_name) AS authors, GROUP_CONCAT(DISTINCT k.keyword) AS keywords, GROUP_CONCAT(DISTINCT c.category_name) AS categories, no_of_copies_in_library, last_update
                                FROM book_in_library bl
                                LEFT JOIN book b ON bl.book_id = b.book_id
                                LEFT JOIN book_author ba ON b.book_id = ba.book_id
                                LEFT JOIN author a ON ba.author_id = a.author_id
                                LEFT JOIN book_keywords bk ON b.book_id = bk.book_id
                                LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
                                LEFT JOIN book_category bc ON b.book_id = bc.book_id
                                LEFT JOIN category c ON bc.category_id = c.category_id
                                WHERE bl.school_id = $school_id
                                GROUP BY b.book_id
                                ORDER BY b.book_title;
	/* search capability*/
	
    SELECT b.*, (
            SELECT GROUP_CONCAT(DISTINCT CONCAT(a.author_first_name, ' ', a.author_last_name))
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id
          ) AS authors, (
            SELECT GROUP_CONCAT(DISTINCT c.category_name)
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id
          ) AS categories, (
            SELECT GROUP_CONCAT(DISTINCT k.keyword)
            FROM book_keywords bk
            LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
            WHERE bk.book_id = b.book_id
          ) AS keywords, bl.no_of_copies_in_library, bl.last_update
          FROM book_in_library bl
          LEFT JOIN book b ON bl.book_id = b.book_id
          WHERE bl.school_id = $school_id AND bl.no_of_copies_in_library > 0
          AND ( b.book_title LIKE '$query%' OR EXISTS (
            SELECT *
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id AND (a.author_first_name LIKE '$query%' OR a.author_last_name LIKE '$query%')
          ) OR EXISTS (
            SELECT *
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id AND c.category_name LIKE '$query%'
          ));
          
          /*rent-reserve queries*/
          
          SELECT no_of_copies_in_library FROM book_in_library WHERE school_id = $school_id AND book_id = '$book_id';
          
          /*if there are available copies deduct and rent */
          UPDATE book_in_library SET no_of_copies_in_library = no_of_copies_in_library - 1 WHERE school_id = $school_id AND book_id = '$book_id';
          
          INSERT INTO book_rent (user_id, book_id, rent_date) VALUES ($user_id, '$book_id', CURDATE());
          
          UPDATE user SET number_of_rentals = number_of_rentals + 1 WHERE user_id = $user_id;
          
          /* else reserve */
          
		  INSERT INTO book_reservation (user_id, book_id, reservation_date) VALUES ($user_id, '$book_id', CURDATE());
          
          UPDATE user SET number_of_reservations = number_of_reservations + 1 WHERE user_id = $user_id;
          

	/* 4.3.2.List of all books borrowed by this user. */
    
          SELECT br.rent_id, br.book_id, b.ISBN, b.book_title, br.rent_date, br.returned_at
		  FROM book_rent br
		  INNER JOIN book b ON br.book_id = b.book_id
		  WHERE br.user_id = $user_id;

            

            