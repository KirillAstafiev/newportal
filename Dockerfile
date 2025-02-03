FROM nginx:latest

COPY nginx/default.conf /etc/nginx/conf.d/
COPY react /usr/share/nginx/html/

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]