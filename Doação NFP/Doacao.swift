//
//  Doacao.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/13/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import Foundation

class Doacao{
    
    var qrcode = ""
    var ong:ONG?
    var data:NSDate
    var comentario = ""
    
    init(qr:String,ong:ONG,comment:String){
        comentario = comment
        qrcode = qr
        self.ong = ong
        data = NSDate()
    }
    
}