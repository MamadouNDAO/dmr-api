.PHONY: deploy

deploy:
	rsync -av ./ xgqnjwrk@node59-eu.n0c.com:5022~/api \
	--include=public/build \
	--include=public/.htaccess \
	--include=vendor \
	--exclude-from=.gitignore \
	--exclude=".*"