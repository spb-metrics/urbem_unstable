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
    * Classe de mapeamento da tabela ima.configuracao_banrisul_local
    * Data de Criação: 02/04/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMAConfiguracaoBanrisulLocal extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoBanrisulLocal()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_banrisul_local");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_convenio,cod_banco,cod_agencia,cod_conta_corrente,timestamp,cod_local');

        $this->AddCampo('cod_convenio'      ,'integer'		 ,true  ,'',true,'TIMAConfiguracaoBanrisulConta');
        $this->AddCampo('cod_banco'         ,'integer'		 ,true  ,'',true,'TIMAConfiguracaoBanrisulConta');
        $this->AddCampo('cod_agencia'       ,'integer'		 ,true  ,'',true,'TIMAConfiguracaoBanrisulConta');
        $this->AddCampo('cod_conta_corrente','integer'		 ,true  ,'',true,'TIMAConfiguracaoBanrisulConta');
        $this->AddCampo('timestamp'         ,'timestamp_now' ,true  ,'',true,'TIMAConfiguracaoBanrisulConta');
        $this->AddCampo('cod_local'         ,'integer'		 ,true  ,'',true,'TOrganogramaLocal');
    }
}
?>
