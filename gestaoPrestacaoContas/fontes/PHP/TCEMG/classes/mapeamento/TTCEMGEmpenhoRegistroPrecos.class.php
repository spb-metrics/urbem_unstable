<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 03/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGEmpenhoRegistroPrecos.class.php 59719 2014-09-08 15:00:53Z franver $
  $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
  $Author: franver $
  $Rev: 59719 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGEmpenhoRegistroPrecos extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGEmpenhoRegistroPrecos()
    {
        parent::Persistente();
        
        $this->setTabela("tcemg.empenho_registro_precos");
        $this->setCampoCod("");
        $this->setComplementoChave("cod_entidade, numero_processo_adesao, exercicio_adesao, cod_empenho, exercicio_empenho");
        
        $this->AddCampo("cod_entidade"          ,"integer",true, "",true,true);
        $this->AddCampo("numero_processo_adesao","integer",true, "",true,true);
        $this->AddCampo("exercicio_adesao"      ,"varchar",true,"4",true,true);
        $this->AddCampo("cod_empenho"           ,"integer",true, "",true,true);
        $this->AddCampo("exercicio_empenho"     ,"varchar",true,"4",true,true);
    }
    
    public function __destruct(){}

}

?>