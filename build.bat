@echo off

echo.
echo        ____  _           ______         __ 
echo       / __ \(_)  _____  / / __/__  ____/ / 
echo      / /_/ / / ^|/_/ _ \/ / /_/ _ \/ __  / 
echo     / ____/ /^>  ^</  __/ / __/  __/ /_/ /  
echo    /_/   /_/_/^|_^|\___/_/_/  \___/\__,_/   
echo    ------------- build process -----------
echo.

WHERE php >nul 2>nul
IF %ERRORLEVEL% NEQ 0 (
  echo !!! PHP command-line executable was not found in the current path.
  echo     It is required in order to build Pixelfed.
  echo.
  exit 255
)

php ./vendor/phing/phing/bin/phing %*
