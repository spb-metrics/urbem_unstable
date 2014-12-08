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
    * Classe de mapeamento da tabela tcemg.valores_identificadoresa
  * Data de Criação   : 17/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: TTCEMGValoresIdentificadores.class.php 59719 2014-09-08 15:00:53Z franver $
  *
  * $Revision: 59719 $
  * $Author: franver $
  * $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TTCEMGValoresIdentificadores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGValoresIdentificadores()
    {
        parent::Persistente();
        $this->setTabela('tcemg.valores_identificadores');
        $this->setComplementoChave('cod_identificador');

        $this->AddCampo('descricao','varchar',true,'4',true,true);
        $this->AddCampo('cod_identificador','integer',true,'',false,false);
    }
    
    public function __destruct(){}

}
