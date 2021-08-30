SELECT 
	paper.id, menu_item_id_fk, headline, content, keywords, editor, updated
    , MATCH (headline, content) AGAINST('grafia') as score 
FROM
	paper
	INNER JOIN
	(SELECT 
		menu_item.id
	FROM
		menu_item
	WHERE
		menu_item.type_fk = 'paper'
			AND menu_item.active = 1
			AND (ISNULL(menu_item.show_time) OR menu_item.show_time <= CURDATE())
			AND (ISNULL(menu_item.hide_time) OR CURDATE() <= menu_item.hide_time)
	) AS menu_item ON (paper.menu_item_id_fk=menu_item.id)
WHERE 
	MATCH(headline, content) AGAINST('grafia') > 0.2 
ORDER BY score DESC