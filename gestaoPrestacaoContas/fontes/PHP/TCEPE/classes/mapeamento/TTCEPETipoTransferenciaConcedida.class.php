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
    * 
    * Data de Criação   : 01/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEProgramas.class.php 60149 2014-10-02 12:35:22Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPETipoTransferenciaConcedida extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    
    public function TTCEPETipoTransferenciaConcedida()
    {
        parent::Persistente();
        $this->setTabela('tcepe.tipo_transferencia_concedida');
        
        $this->setCampoCod('cod_tipo_tcepe');
        $this->setComplementoChave('cod_lote, cod_entidade, exercicio, tipo');
        
        $this->AddCampo('cod_lote'                 , 'integer' , true , ''  , true  , true );
        $this->AddCampo('cod_entidade'             , 'integer' , true , ''  , true  , true );
        $this->AddCampo('exercicio'                , 'char'    , true , '4' , true  , true );
        $this->AddCampo('tipo'                     , 'char'    , true , '1' , true  , true );
        $this->AddCampo('cod_tipo_tcepe'           , 'integer' , true , ''  , true  , true );
        $this->AddCampo('cod_entidade_beneficiada' , 'integer' , true , ''  , false , true );
        
    }

}
?>