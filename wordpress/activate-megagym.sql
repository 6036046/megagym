UPDATE wp_options SET option_value = 'megagym' WHERE option_name IN ('template','stylesheet');
UPDATE wp_options SET option_value = 'http://localhost:3000' WHERE option_name IN ('home','siteurl');
UPDATE wp_options SET option_value = 'a:1:{i:0;s:35:"gym-community-tools/gym-community-tools.php";}' WHERE option_name = 'active_plugins';
