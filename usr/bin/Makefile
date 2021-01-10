OPTIONS = -std=c99 -lpthread `mysql_config --libs` -L/usr/local/opt/openssl/lib

#UNAME_S := $(shell uname -s)
#ifeq ($(UNAME_S),Linux)
#  OPTIONS += -lrt
#endif

#$(OPTIONS += )

$(info $(OPTIONS))

all: batmon

batmon: batmon.c 
	$(CC) $^ $(OPTIONS) -o $@

clean:
	rm batmon

