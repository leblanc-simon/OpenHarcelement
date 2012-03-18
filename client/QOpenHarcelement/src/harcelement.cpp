/*
Copyright (c) 2012, Simon Leblanc
All rights reserved.

Redistribution and use in source and binary forms, with or without modification
, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
    this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice
    , this list of conditions and the following disclaimer in the documentation
     and/or other materials provided with the distribution.
    * Neither the name of the Simon Leblanc nor the names of its contributors
    may be used to endorse or promote products derived from this software
    without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

#include "harcelement.h"
#include "mainwindow.h"

#include <QtNetwork/QHttp>
#include <QtNetwork/QHttpResponseHeader>
#include <QtNetwork/QHttpRequestHeader>
#include <QUrl>
#include <QObject>
#include <QByteArray>
#include <QSettings>

Harcelement::Harcelement(MainWindow *main_window)
{
    this->main_window   = main_window;
    this->hash          = "";
    this->name          = "";
    this->email         = "";
    this->email_victim  = "";
    this->subject       = "";
    this->message       = "";
    this->time          = "";

    QSettings settings(CONFIG_FILE, QSettings::IniFormat);

    this->url_api       = QUrl(settings.value("api_url", "").toString());
}


bool Harcelement::callAdd()
{
    QByteArray content(this->buildRequest().toUtf8());

    QHttpRequestHeader header("POST", this->url_api.path());
    header.setValue("Host", this->url_api.host());
    header.setContentType("application/x-www-form-urlencoded");
    header.setContentLength(content.length());

    QHttp *http = new QHttp(this->getHost());

    http->request(header, content);

    QObject::connect(http, SIGNAL(done(bool)), this->main_window, SLOT(state(bool)));
    QObject::connect(http, SIGNAL(requestFinished(int,bool)), this->main_window, SLOT(httpFinished(int)));
    QObject::connect(http, SIGNAL(responseHeaderReceived(QHttpResponseHeader)), this->main_window, SLOT(readHeader(QHttpResponseHeader)));

    return true;
}

bool Harcelement::callDelete()
{
    if (this->hash.isEmpty()) {
        return false;
    }

    QByteArray content("");

    QHttpRequestHeader header("DELETE", this->url_api.path() + QString("?%1").arg(QString(QUrl::toPercentEncoding(this->hash))).toUtf8());
    header.setValue("Host", this->url_api.host());
    header.setContentType("application/x-www-form-urlencoded");
    header.setContentLength(content.length());

    QHttp *http = new QHttp(this->getHost());

    http->request(header, content);

    QObject::connect(http, SIGNAL(done(bool)), this->main_window, SLOT(state(bool)));
    QObject::connect(http, SIGNAL(requestFinished(int,bool)), this->main_window, SLOT(httpFinished(int)));
    QObject::connect(http, SIGNAL(responseHeaderReceived(QHttpResponseHeader)), this->main_window, SLOT(readHeader(QHttpResponseHeader)));

    return true;
}


QString Harcelement::getHost()
{
    return this->url_api.host();
}

quint16 Harcelement::getPort()
{
    return this->url_api.port() == -1 ? 0 : this->url_api.port();
}

QString Harcelement::buildRequest()
{
    QString data;

    data = QString("name=%1").arg(QString(QUrl::toPercentEncoding(this->name))) +
           QString("&email=%1").arg(QString(QUrl::toPercentEncoding(this->email))) +
           QString("&email_victim=%1").arg(QString(QUrl::toPercentEncoding(this->email_victim))) +
           QString("&subject=%1").arg(QString(QUrl::toPercentEncoding(this->subject))) +
           QString("&message=%1").arg(QString(QUrl::toPercentEncoding(this->message))) +
           QString("&time=%1").arg(QString(QUrl::toPercentEncoding(this->time)));

    return data;
}
