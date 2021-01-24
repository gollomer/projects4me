FROM gcr.io/google-appengine/php

ENV DOCUMENT_ROOT /app/public

RUN composer clear-cache
RUN composer -vvv
