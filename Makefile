MODULE_NAME := telegram_3b6df
ZIP_NAME := $(MODULE_NAME).zip
DIST_DIR := ../xc_vm/modules_archives

all: zip

zip:
	@mkdir -p $(DIST_DIR)
	@rm -f $(DIST_DIR)/$(ZIP_NAME)
	@cd .. && zip -r $(DIST_DIR)/$(ZIP_NAME) Module_Telegram/ -x "Module_Telegram/.git/*" "Module_Telegram/Makefile" "Module_Telegram/README.md"
	@echo "✓ Packaged to $(DIST_DIR)/$(ZIP_NAME)"
