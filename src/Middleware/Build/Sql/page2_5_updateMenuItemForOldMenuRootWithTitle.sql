UPDATE menu_item AS Me
SET Me.list='{{new_menu_list}}'
WHERE Me.list = '{{old_menu_list}}'