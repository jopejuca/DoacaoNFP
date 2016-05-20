//
//  DoacaoSingleton.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/13/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import Foundation

class DoacaoSingleton {
    
    static let sharedInstance = DoacaoSingleton()
    
    var historico:[Doacao]
    
    // METHODS
    private init() {
        historico = [Doacao]();
    }
}
